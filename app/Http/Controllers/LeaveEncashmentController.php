<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmployeeLeave;
use App\Models\LeaveEncashment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeaveEncashmentImport;
use App\Exports\LeaveEncashmentImportResultExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use App\Exports\LeaveEncashmentSampleExport;
use PDF;

class LeaveEncashmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employees = User::role('employee')->get();
        $query = LeaveEncashment::with('employee')->latest();
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        $encashments = $query->get();
        return view('admin.employee-leave-encashment.index', compact('encashments', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = User::role('employee')->get();
        return view('admin.employee-leave-encashment.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'encash_leaves' => 'required|integer|min:1',
            'pending_leaves' => 'required|numeric|min:0',
        ]);

        if ($request->encash_leaves > $request->pending_leaves) {
            return redirect()->back()->withInput()->withErrors([
                'encash_leaves' => 'Encash leaves cannot be greater than pending leaves (' . $request->pending_leaves . ').'
            ]);
        }

        LeaveEncashment::create([
            'employee_id' => $request->employee_id,
            'pending_leaves' => $request->pending_leaves,
            'encash_leaves' => $request->encash_leaves,
        ]);

        return redirect()->route('employee-leave-encashment.index')->with('success', 'Leave Encashment recorded.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveEncashment $leaveEncashment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $encashment = LeaveEncashment::with('employee')->findOrFail($id);
        $employees = User::role('employee')->get();
        return view('admin.employee-leave-encashment.edit', compact('encashment', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'encash_leaves' => 'required|integer|min:1',
            'pending_leaves' => 'required|numeric|min:0',
        ]);

        if ($request->encash_leaves > $request->pending_leaves) {
            return redirect()->back()
                ->withErrors(['encash_leaves' => 'Encash leaves cannot be greater than pending leaves (' . $request->pending_leaves . ').'])
                ->withInput();
        }

        $encashment = LeaveEncashment::findOrFail($id);

        $encashment->update([
            'employee_id' => $request->employee_id,
            'pending_leaves' => $request->pending_leaves,
            'encash_leaves' => $request->encash_leaves,
        ]);

        return redirect()->route('employee-leave-encashment.index')->with('success', 'Leave Encashment updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($encashment_id)
    {
        LeaveEncashment::where('id', $encashment_id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee leave encashment record deleted successfully!'
        ]);
    }

    public function getPendingLeaves(Request $request)
    {
        $employeeId = $request->employee_id;

        if (!$employeeId) {
            return response()->json(['pending_leaves' => 0]);
        }
        $leaves = EmployeeLeave::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where('date', '>=', now()->subYear())
            ->get();

        $usedLeaves = $leaves->sum(function ($leave) {
            return $leave->type === 'Half Day' ? 0.5 : 1;
        });
        $encashedLeaves = DB::table('leave_encashments')
            ->where('employee_id', $employeeId)
            ->sum('encash_leaves');
        // dd( $usedLeaves);
        $pendingLeaves = max(0, 10 - $usedLeaves - $encashedLeaves);

        return response()->json(['pending_leaves' => $pendingLeaves]);
    }

   
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,csv',
        ]);

        try {
            $import = new LeaveEncashmentImport;
            Excel::import($import, $request->file('import_file'));

            $results = $import->getResults();

            $successCount = collect($results)->where('status', 'Success')->count();
            $failCount = collect($results)->where('status', 'Failed')->count();

            Session::flash('message', "$successCount rows imported successfully. $failCount rows failed.");
            Session::flash('alert-type', $failCount > 0 ? 'warning' : 'success');

            $filename = 'leave_encashment_import_result_' . now()->format('Ymd_His') . '.xlsx';
            return Excel::download(new LeaveEncashmentImportResultExport($results), $filename);

        } catch (\Exception $e) {
            return redirect()->back()->with([
                'message' => 'Import failed: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }
public function downloadSample()
{
    return Excel::download(new LeaveEncashmentSampleExport, 'leave_encashment_sample.xlsx');
}
}
