<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use App\Models\DeductionDetail;
use App\Models\User;
use App\Models\FortnightDates;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Exports\DeductionsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;

class DeductionController extends Controller
{
    public function index()
    {
        if (!Gate::allows('view nst deduction')) {
            abort(403);
        }
        $deductions = Deduction::with('user')->latest()->get();

        return view('admin.deductions.index', compact('deductions'));
    }

    public function getDeductionsData(Request $request)
    {
        $deductions = Deduction::with('user');

        if ($request->has('search_name') && !empty($request->search_name)) {
            $deductions->whereHas('user', function ($query) use ($request) {
                $query->where('first_name', 'like', '%' . $request->search_name . '%');
            });
        }

        if ($request->has('search_type') && !empty($request->search_type)) {
            $deductions->where('type', 'like', '%' . $request->search_type . '%');
        }

        if ($request->has('search_document_date') && !empty($request->search_document_date)) {
            $deductions->whereDate('document_date', Carbon::parse($request->search_document_date)->format('Y-m-d'));
        }

        if ($request->has('search_period_date') && !empty($request->search_period_date)) {
            $deductions->whereDate('start_date', '<=', Carbon::parse($request->search_period_date)->format('Y-m-d'))
                ->whereDate('end_date', '>=', Carbon::parse($request->search_period_date)->format('Y-m-d'));
        }

        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $deductions->where(function ($query) use ($searchValue) {
                $query->where('type', 'like', '%' . $searchValue . '%')
                    ->orWhere('amount', 'like', '%' . $searchValue . '%')
                    ->orWhere('no_of_payroll', 'like', '%' . $searchValue . '%')
                    ->orWhere('start_date', 'like', '%' . $searchValue . '%')
                    ->orWhere('end_date', 'like', '%' . $searchValue . '%')
                    ->orWhereHas('user', function ($subQuery) use ($searchValue) {
                        $subQuery->where('user_code', 'like', '%' . $searchValue . '%')
                            ->orWhere('first_name', 'like', '%' . $searchValue . '%');
                    });
            });
        }

        $totalRecords = Deduction::count();

        $filteredRecords = $deductions->count();

        $length = $request->input('length', 10);
        $start = $request->input('start', 0);

        $deductions = $deductions->skip($start)->take($length)->get()->map(function ($item) {
            $item->amount = formatAmount($item->amount);
            return $item;
        });

        $data = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $deductions,
        ];

        return response()->json($data);
    }

    public function create()
    {
        if (!Gate::allows('create nst deduction')) {
            abort(403);
        }
        $userRole = Role::where('id', 3)->first();

        $securityGuards = User::with('guardAdditionalInformation')->whereHas('roles', function ($query) use ($userRole) {
            $query->where('role_id', $userRole->id);
        })->where('status', 'Active')->latest()->get();

        return view('admin.deductions.create', compact('securityGuards'));
    }

    public function getEndDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($request->date);
        $noOfPayrolls = $request->no_of_payroll ?? 1;
        $fortnightStart = FortnightDates::where('start_date', '<=', $date)->orderBy('start_date', 'desc')->first();
        $nextFortnightDate = Carbon::parse($fortnightStart->start_date)->addDays(14);
        $fortnights  = FortnightDates::where('start_date', '>=', $nextFortnightDate)->orderBy('start_date', 'asc')->limit($noOfPayrolls)->get();

        if (!$fortnightStart) {
            return response()->json(['error' => 'No matching fortnight found for the selected start date.'], 404);
        }

        $endDate = Carbon::parse($fortnights->last()->end_date);
        $startDate = Carbon::parse($nextFortnightDate)->format('d-m-Y');

        return response()->json(['end_date' => $endDate->format('d-m-Y'), 'start_date' => $startDate]);
    }

    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function store(Request $request)
    {
        if (!Gate::allows('create nst deduction')) {
            abort(403);
        }
        $request->validate([
            'guard_id'    => 'required|exists:users,id',
            'type'        => 'required|string',
            'amount'      => 'required|numeric|min:0',
            'document_date' => 'required|date',
            'start_date'  => 'required|date|after_or_equal:document_date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'no_of_payroll'   => 'nullable|integer|min:1',
            'guard_document' => 'nullable|file',
        ]);

        /*$noOfPayrolls = $request->no_of_payroll ?? 1;
        $oneInstallment = $request->amount / $noOfPayrolls;

        $existingDeduction = Deduction::where('guard_id', $request->guard_id)
            ->where('type', $request->type)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$this->parseDate($request->start_date), $this->parseDate($request->end_date)])
                    ->orWhereBetween('end_date', [$this->parseDate($request->start_date), $this->parseDate($request->end_date)])
                    ->orWhere(function ($query) use ($request) {
                        $query->where('start_date', '<=', $this->parseDate($request->start_date))
                            ->where('end_date', '>=', $this->parseDate($request->end_date));
                    });
            })
            ->exists();*/

        $noOfPayrolls = $request->no_of_payroll;
        $oneInstallment = $noOfPayrolls ? ($request->amount / $noOfPayrolls) : $request->amount;

        $existingDeductionQuery = Deduction::where('guard_id', $request->guard_id)
            ->where('type', $request->type);

        if ($request->filled('end_date')) {
            $existingDeductionQuery->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [
                    $this->parseDate($request->start_date),
                    $this->parseDate($request->end_date)
                ])
                    ->orWhereBetween('end_date', [
                        $this->parseDate($request->start_date),
                        $this->parseDate($request->end_date)
                    ])
                    ->orWhere(function ($query) use ($request) {
                        $query->where('start_date', '<=', $this->parseDate($request->start_date))
                            ->where('end_date', '>=', $this->parseDate($request->end_date));
                    });
            });
        }

        $existingDeduction = $existingDeductionQuery->exists();

        if ($existingDeduction) {
            return redirect()->route('deductions.index')
                ->with('error', 'A deduction of this type already exists for this guard.');
        }

        if ($existingDeduction) {
            return redirect()->route('deductions.index')->with('error', 'A deduction of this type already exists for this guard.');
        }

        $guardDocumentPath = null;
        if ($request->hasFile('guard_document')) {
            $file = $request->file('guard_document');
            $filename = time() . '_' . $file->getClientOriginalName();
            $guardDocumentPath = $file->storeAs('guard_documents', $filename, 'public');
        }

        Deduction::create([
            'guard_id'     => $request->guard_id,
            'type'         => $request->type,
            'amount'       => $request->amount,
            'no_of_payroll' => $noOfPayrolls,
            'document_date' => $request->document_date,
            'start_date'   => $this->parseDate($request->start_date),
            'end_date'     => $this->parseDate($request->end_date),
            'one_installment' => $oneInstallment,
            'pending_balance' => $request->amount,
            'guard_document'   => $guardDocumentPath,
        ]);

        return redirect()->route('deductions.index')->with('success', 'Deduction created successfully.');
    }

    public function exportDeduction(Request $request)
    {
        $searchName = $request->input('search_name');
        $searchDocumentDate = $request->input('search_document_date');
        $searchPeriodDate = $request->input('search_period_date');

        $query = DeductionDetail::with('deduction', 'user');
        if ($searchName) {
            $query->whereHas('user', function ($q) use ($searchName) {
                $q->where('first_name', 'like', '%' . $searchName . '%');
            });
        }

        if ($request->has('search_type') && !empty($request->search_type)) {
            $query->whereHas('deduction', function ($q) use ($request) {
                $q->where('type', $request->search_type);
            });
        }

        if ($searchDocumentDate) {
            $query->whereHas('deduction', function ($q) use ($searchDocumentDate) {
                $q->whereDate('document_date', '=', carbon::parse($searchDocumentDate)->format('Y-m-d'));
            });
        }

        if ($searchPeriodDate) {
            $query->whereHas('deduction', function ($q) use ($searchPeriodDate) {
                $q->whereDate('start_date', '<=', Carbon::parse($searchPeriodDate)->format('Y-m-d'))
                    ->whereDate('end_date', '>=', Carbon::parse($searchPeriodDate)->format('Y-m-d'));
            });
        }
        $deductionDetails = $query->get();

        return Excel::download(new DeductionsExport($deductionDetails), 'deductions_report.xlsx');
    }
}
