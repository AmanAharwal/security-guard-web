<?php

namespace App\Console\Commands;

use App\Models\Deduction;
use Illuminate\Console\Command;
use App\Models\FortnightDates;
use App\Models\GuardRoster;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\PublicHoliday;
use Carbon\Carbon;
use App\Models\Punch;
use App\Models\User;
use Google\Service\Sheets\NumberFormat;
use Spatie\Permission\Models\Role;

class PublishGuardRoaster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish:guard-roaster';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Guard Roaster';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now()->startOfDay();
        $fortnightDays = FortnightDates::whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today)->first();        
        if ($fortnightDays) {
            $endDate = Carbon::parse($fortnightDays->end_date)->startOfDay();
            $differenceInDays = $today->diffInDays($endDate, false); 
            $nextStartDate = Carbon::parse($fortnightDays->end_date)->addDay();
            $nextEndDate = $nextStartDate->copy()->addDays(13);

            $sixthDay = Carbon::parse($fortnightDays->start_date)->addDays(6);
            $isPublishDate =  Carbon::parse($sixthDay)->addDays(3);

            if ($sixthDay = $today) {
                $previousFortnightEndDate = Carbon::parse($fortnightDays->start_date)->subDay();
                $previousFortnightStartDate = $previousFortnightEndDate->copy()->subDays(13);

                $publicHolidays = PublicHoliday::whereBetween('date', [$previousFortnightStartDate, $previousFortnightEndDate])->pluck('date')->toArray();
                $previousStartDate = $previousFortnightStartDate->format('Y-m-d');
                $previousEndDate = $previousFortnightEndDate->format('Y-m-d');

                $attendances = Punch::with('user')->whereDate('in_time', '>=', $previousStartDate)->whereDate('in_time', '<=', $previousEndDate)->latest()
                                    ->select('id', 'user_id', 'guard_type_id', 'in_time', 'out_time', 'regular_rate', 'laundry_allowance', 'canine_premium', 'fire_arm_premium', 'gross_hourly_rate', 'overtime_rate', 'holiday_rate')->get();

                $groupedAttendances = $attendances->groupBy('user_id');

                $userHours = [];
                foreach ($groupedAttendances as $userId => $attendancesForUser)
                {
                    $attendanceDetails = $attendancesForUser->groupBy(function ($attendance) {
                        return Carbon::parse($attendance->in_time)->toDateString();
                    })->toArray();

                    $userHours[$userId] = $this->calculateUserHours($userId, $attendanceDetails, $publicHolidays, $previousStartDate, $previousEndDate);

                    $existingPayroll = Payroll::where('guard_id', $userId)->where('start_date', $previousFortnightStartDate->format('Y-m-d'))
                                                ->where('end_date', $previousFortnightEndDate->format('Y-m-d'))->first();

                if (!$existingPayroll) {
                    $payrollData = Payroll::create([
                        'guard_id'              => $userId,
                        'start_date'            => $previousFortnightStartDate->format('Y-m-d'),
                        'end_date'              => $previousFortnightEndDate->format('Y-m-d'),
                        'normal_hours'          => $userHours[$userId]['total_normal_hours'],
                        'overtime'              => $userHours[$userId]['total_overtime_hours'],
                        'public_holidays'       => $userHours[$userId]['total_public_holiday_hours'],
                        'normal_hours_rate'     => $userHours[$userId]['total_normal_earnings'],
                        'overtime_rate'                 => $userHours[$userId]['total_overtime_earnings'],
                        'public_holiday_rate'           => $userHours[$userId]['total_public_holiday_earnings'],
                        'gross_salary_earned'           => $userHours[$userId]['gross_salary_earned'],
                        'less_nis'                      => $userHours[$userId]['less_nis'],
                        'employer_contribution_nis_tax' => $userHours[$userId]['employer_contribution_nis_tax'],
                        'statutory_income'              => $userHours[$userId]['statutory_income'],
                        'education_tax'                 => $userHours[$userId]['education_tax'],
                        'employer_eduction_tax'         => $userHours[$userId]['employer_eduction_tax'],
                        'nht'                           => $userHours[$userId]['nht'],
                        'employer_contribution_nht_tax' => $userHours[$userId]['employer_contribution_nht_tax'],
                        'paye'                          => $userHours[$userId]['paye'],
                        'heart'                         => $userHours[$userId]['heart'],
                        'threshold'                     => $userHours[$userId]['threshold'],
                        'staff_loan'                    => $userHours[$userId]['staff_loan'],
                        'medical_insurance'             => $userHours[$userId]['medical_insurance'],
                        'approved_pension_scheme'       => $userHours[$userId]['approved_pension'],
                        'salary_advance'                => $userHours[$userId]['salary_advance'],
                        'psra'                          => $userHours[$userId]['psra'],
                        'bank_loan'                     => $userHours[$userId]['bank_loan'],
                        // 'other_deduction'               => $userHours[$userId]['other_deduction'],
                        'pending_staff_loan'            => $userHours[$userId]['pending_staff_loan'],
                        'pending_medical_insurance'     => $userHours[$userId]['pending_medical_insurance'],
                        'pending_salary_advance'        => $userHours[$userId]['pending_salary_advance'],
                        'pending_psra'                  => $userHours[$userId]['pending_psra'],
                        'pending_bank_loan'             => $userHours[$userId]['pending_bank_loan'],
                        'pending_approved_pension'      => $userHours[$userId]['pending_approved_pension'],
                        // 'pending_other_deduction'       => $userHours[$userId]['pending_other_deduction'],
                        'garnishment'                   => $userHours[$userId]['garnishment'],
                        'missing_goods'                 => $userHours[$userId]['missing_goods'],
                        'damaged_goods'                 => $userHours[$userId]['damaged_goods'],
                        'pending_garnishment'           => $userHours[$userId]['pending_garnishment'],
                        'pending_missing_goods'         => $userHours[$userId]['pending_missing_goods'],
                        'pending_damaged_goods'         => $userHours[$userId]['pending_damaged_goods'],
                    ]);
                } else {
                    $payrollData = $existingPayroll;
                }
                    $this->createPayrollDetails($payrollData->id, $userId, $attendanceDetails, $publicHolidays);
                }
            }

            if ($isPublishDate = $today) {
                $previousFortnightEndDate = Carbon::parse($fortnightDays->start_date)->subDay();
                $previousFortnightStartDate = $previousFortnightEndDate->copy()->subDays(13);

                $payrollPublished = Payroll::where('start_date', $previousFortnightStartDate->format('Y-m-d'))
                                            ->where('end_date', $previousFortnightEndDate->format('Y-m-d'))->get();

                foreach ($payrollPublished as $payroll) {
                    $payroll->update([
                        'is_publish' => 1
                    ]);
                }
            } else if ($differenceInDays == 2) {
                $roster = GuardRoster::where('date', '>=', $fortnightDays->start_date)->where('end_date', '<=', $fortnightDays->end_date)->get();

                $nextFortnightRoster = GuardRoster::whereDate('date', '>=', $nextStartDate)->whereDate('end_date', '<=', $nextEndDate)->get();
                if ($nextFortnightRoster->isEmpty()) {
                    foreach ($roster as $currentRoster) {
                        $shiftedDate = Carbon::parse($currentRoster->date)->addDays(14);
                        $startTime = Carbon::parse($currentRoster->start_time);
                        $endTime = Carbon::parse($currentRoster->end_time);

                        $existingRoster = GuardRoster::where('guard_id', $currentRoster->guard_id)->where('client_site_id', $currentRoster->client_site_id)->where('date', '=', $shiftedDate->format('Y-m-d'))->first();

                        if ($existingRoster) {
                            continue;
                        }

                        $endDate = $shiftedDate->copy();
                        if ($endTime->lessThan($startTime)) {
                            $endDateForNextRoster = $endDate->addDay();
                        } else {
                            $endDateForNextRoster = $endDate;
                        }

                        GuardRoster::create([
                            'guard_id' => $currentRoster->guard_id,
                            'client_id' => $currentRoster->client_id,
                            'client_site_id' => $currentRoster->client_site_id,
                            'date' => $shiftedDate->format('Y-m-d'),
                            'end_date' => $endDateForNextRoster->format('Y-m-d'),
                            'start_time' => $currentRoster->start_time,
                            'end_time' => $currentRoster->end_time,
                        ]);
                    }
                }
            } else if ($differenceInDays == 1) {
                $newFortnightRoster = GuardRoster::whereDate('date', '>=', $nextStartDate)->whereDate('end_date', '<=', $nextEndDate)->get();
                foreach ($newFortnightRoster as $currentRoster) {
                    $currentRoster->update([
                        'is_publish' => 1
                    ]);
                }
            }
        }
    }

    protected function calculateUserHours($userId, $attendanceDetails, $publicHolidays,  $previousStartDate, $previousEndDate)
    {
        $totalNormalHours = 0;
        $totalNormalMinutes = 0;
        $totalOvertimeHours = 0;
        $totalOvertimeMinutes = 0;
        $totalPublicHolidayHours = 0;
        $totalPublicHolidayMinutes = 0;
        $regularHours = 8;

        $totalNormalEarnings = 0;
        $totalOvertimeEarnings = 0;
        $totalPublicHolidayEarnings = 0;

        foreach ($attendanceDetails as $attendanceDate => $attendancesForDay) {
            $totalWorkedMinutesForDay = 0;

            foreach ($attendancesForDay as $attendance) {
                $inTime = Carbon::parse($attendance['in_time']);
                $outTime = Carbon::parse($attendance['out_time']);

                $workedMinutes = $inTime->diffInMinutes($outTime);
                $totalWorkedMinutesForDay += $workedMinutes;
    
                $isPublicHoliday = in_array($attendanceDate, $publicHolidays);
    
                $rateMaster = $attendance;
                if ($isPublicHoliday) {
                    $totalPublicHolidayEarnings += ($workedMinutes / 60) * $rateMaster['holiday_rate'];
                } else {
                    if ($workedMinutes <= $regularHours * 60) {
                        $totalNormalEarnings += ($workedMinutes / 60) * $rateMaster['gross_hourly_rate'];
                    } else {
                        $normalMinutes = $regularHours * 60;
                        $overtimeMinutes = $workedMinutes - $normalMinutes;
    
                        $totalNormalEarnings += ($normalMinutes / 60) * $rateMaster['gross_hourly_rate'];
                        $overTimeHours = ($overtimeMinutes / 60);
                        $totalOvertimeEarnings += ($overtimeMinutes / 60) * $rateMaster['overtime_rate'];
                    }
                }
            }

            $isPublicHoliday = in_array($attendanceDate, $publicHolidays);

            if ($isPublicHoliday) {
                $totalPublicHolidayMinutes += $totalWorkedMinutesForDay;
            } else {
                if ($totalWorkedMinutesForDay <= $regularHours * 60) {
                    $totalNormalMinutes += $totalWorkedMinutesForDay;
                } else {
                    $totalNormalMinutes += $regularHours * 60;
                    $totalOvertimeMinutes += ($totalWorkedMinutesForDay - ($regularHours * 60));
                }
            }
        }

        $extraNormalHours = intdiv($totalNormalMinutes, 60);
        $totalNormalHours = $extraNormalHours;
        $totalNormalMinutes = $totalNormalMinutes % 60;

        $extraOvertimeHours = intdiv($totalOvertimeMinutes, 60);
        $totalOvertimeHours = $extraOvertimeHours;
        $totalOvertimeMinutes = $totalOvertimeMinutes % 60;

        $extraPublicHolidayHours   = intdiv($totalPublicHolidayMinutes, 60);
        $totalPublicHolidayHours   = $extraPublicHolidayHours;
        $totalPublicHolidayMinutes = $totalPublicHolidayMinutes % 60;

        $grossSalaryEarned     = $totalNormalEarnings + $totalOvertimeEarnings + $totalPublicHolidayEarnings;
        $approvedPensionScheme = 0;

        $userData = User::with('guardAdditionalInformation')->where('id', $userId)->first();
        $dateOfBirth = $userData->guardAdditionalInformation->date_of_birth;
        $birthDate = Carbon::parse($dateOfBirth);
        $age = $birthDate->age;

        $currentYear = Carbon::now()->year;
        $fullYearNis = Payroll::where('guard_id', $userId)->whereYear('created_at', $currentYear)->get();

        $payeIncome = 0;
        $employer_contribution = 0;
        $employerContributionNht = 0;
        $nhtDeduction = 0;
        $eduction_tax = 0;
        $hearttax = 0;
        $lessNis = 0;
        $employerContributionNis = 0;

        if($userData->is_statutory == 0) {
            $totalNisForCurrentYear = $fullYearNis->sum('less_nis');
           
            if ($age >= 70) {
                $lessNis = 0;
                $employerContributionNis = 0;
            } else {
                if ($totalNisForCurrentYear < 150000) {
                    $nisDeduction = $grossSalaryEarned * 0.03;
                    $remainingNisToReachLimit = 150000 - $totalNisForCurrentYear;
                    if ($nisDeduction > $remainingNisToReachLimit) {
                        $lessNis = $remainingNisToReachLimit;
                    } else {
                        $lessNis = $nisDeduction;
                    }
                } else {
                    $lessNis = 0;
                }
                $employerContributionNis = $grossSalaryEarned * 0.03;
            }

            $statutoryIncome  = $grossSalaryEarned -  $lessNis - $approvedPensionScheme;
    
            if ($statutoryIncome < 65389) {
                $payeIncome = 0;
            } elseif ($statutoryIncome > 65389 && $statutoryIncome <= 230769.23) {
                $payeData = $statutoryIncome - 65389;
                $payeIncome = $payeData * 0.25;
            } elseif($statutoryIncome > 230770.23) {
                $payeData = $statutoryIncome - 230770.23;
                $payeIncome = $payeData * 0.30;
            }

            $eduction_tax = $statutoryIncome * 0.0225;
            $employer_contribution = $grossSalaryEarned * 0.035;
           
            if ($age >= 65) {
                $nhtDeduction = 0;
                $employerContributionNht = 0;
            } else {
                $nhtDeduction = $grossSalaryEarned * 0.02;
                $employerContributionNht =  $grossSalaryEarned * 0.03;
            }

            $hearttax = $grossSalaryEarned * 0.035;
        } else {
            $statutoryIncome  = $grossSalaryEarned -  $lessNis - $approvedPensionScheme;
        }

        if ($userData->is_statutory == 1) {
            $deductionTypes = [
                'Staff Loan'        => 'pending_staff_loan',
                'Medical Ins'       => 'pending_medical_insurance',
                'Salary Advance'    => 'pending_salary_advance',
                'PSRA'              => 'pending_psra',
                'Bank Loan'         => 'pending_bank_loan',
                'Approved Pension'  => 'pending_approved_pension',
                // 'Other deduction'   => 'pending_other_deduction',
                'Garnishment'       => 'pending_garnishment',
                'Missing Goods'     => 'pending_missing_goods',
                'Damaged Goods'     => 'pending_damaged_goods'
            ];
        
            $totalDeductions = array_fill_keys(array_keys($deductionTypes), 0);
            $pendingAmounts = array_fill_keys(array_keys($deductionTypes), 0);
        
            foreach ($deductionTypes as $deductionType => $pendingField) {
                $deductionRecords = Deduction::where('guard_id', $userId)->where('type', $deductionType)->whereDate('start_date', '<=', $previousEndDate)->whereDate('end_date', '>=', $previousStartDate)->get();
        
                foreach ($deductionRecords as $deduction) {
                    if ($deduction->start_date <= $previousEndDate && $deduction->end_date >= $previousStartDate) {
                        $totalDeductions[$deductionType] += $deduction->one_installment;
                        $pendingBalance = $deduction->pending_balance - $deduction->one_installment;
                        $pendingAmounts[$deductionType] += $deduction->pending_balance - $deduction->one_installment;
                       
                        $deduction->update([
                            'pending_balance' => $pendingBalance
                        ]);
                    }
                }
            }

            $pendingStaffLoan = $pendingAmounts['Staff Loan'];
            $pendingMedicalInsurance = $pendingAmounts['Medical Ins'];
            $pendingSalaryAdvance = $pendingAmounts['Salary Advance'];
            $pendingPsra = $pendingAmounts['PSRA'];
            $pendingBankLoan = $pendingAmounts['Bank Loan'];
            $pendingApprovedPension = $pendingAmounts['Approved Pension'];
            // $pendingOtherDeduction = $pendingAmounts['Other deduction'];
            $pendingGarnishment = $pendingAmounts['Garnishment'];
            $pendingMissingGoods = $pendingAmounts['Missing Goods'];
            $pendingDamagedGoods = $pendingAmounts['Damaged Goods'];


            $staffLoan = $totalDeductions['Staff Loan'];
            $medicalInsurance = $totalDeductions['Medical Ins'];
            $salaryAdvance = $totalDeductions['Salary Advance'];
            $psra = $totalDeductions['PSRA'];
            $bankLoan = $totalDeductions['Bank Loan'];
            $approvedPension = $totalDeductions['Approved Pension'];
            // $otherDeduction = $totalDeductions['Other deduction'];
            $garnishment = $totalDeductions['Garnishment'];
            $missingGoods  = $totalDeductions['Missing Goods'];
            $damagedGoods = $totalDeductions['Damaged Goods'];
        }

        $educationTax          = $eduction_tax;
        $employerEductionTax   = $employer_contribution;
        $nht                   = $nhtDeduction;
        $employerContributionNhtTax = $employerContributionNht;
        $paye                  = $payeIncome;
        $heart                 = $hearttax;
        $nis                   = $lessNis;
        $threshold             = 0;

        return [
            'total_normal_hours'            => $totalNormalHours . '.' . str_pad($totalNormalMinutes, 2, '0', STR_PAD_LEFT),
            'total_overtime_hours'          => $totalOvertimeHours . '.' . str_pad($totalOvertimeMinutes, 2, '0', STR_PAD_LEFT),
            'total_public_holiday_hours'    => $totalPublicHolidayHours . '.' . str_pad($totalPublicHolidayMinutes, 2, '0', STR_PAD_LEFT),
            'total_normal_earnings'         => number_format($totalNormalEarnings, 2, '.', ''),
            'total_overtime_earnings'       => number_format($totalOvertimeEarnings, 2, '.', ''),
            'total_public_holiday_earnings' => number_format($totalPublicHolidayEarnings, 2, '.', ''),
            'gross_salary_earned'           => number_format($grossSalaryEarned, 2, '.', ''),
            'less_nis'                      => number_format($nis, 2, '.', ''),
            'employer_contribution_nis_tax' => number_format($nis + $employerContributionNis , 2, '.', ''),
            'approved_pension_scheme'       => number_format($approvedPensionScheme, 2, '.', ''),
            'statutory_income'              => number_format($statutoryIncome, 2, '.', ''),
            'education_tax'                 => number_format($educationTax, 2, '.', ''),
            'employer_eduction_tax'         => number_format($educationTax + $employerEductionTax, 2, '.', ''),
            'nht'                           => number_format($nht, 2, '.', ''),
            'employer_contribution_nht_tax' => number_format($nht + $employerContributionNhtTax, 2, '.', ''),
            'paye'                          => number_format($paye, 2, '.', ''),
            'staff_loan'                    => number_format($staffLoan, 2, '.', ''),
            'medical_insurance'             => number_format($medicalInsurance, 2, '.', ''),
            'salary_advance'                => number_format($salaryAdvance, 2, '.', ''),
            'psra'                          => number_format($psra, 2, '.', ''),
            'bank_loan'                     => number_format($bankLoan, 2, '.', ''),
            'approved_pension'              => number_format($approvedPension, 2, '.', ''),
            'threshold'                     => number_format($threshold, 2, '.', ''),
            // 'other_deduction'               => number_format($otherDeduction, 2, '.', ''),
            'heart'                         => number_format($heart, 2, '.', ''),
            'pending_staff_loan'            => number_format($pendingStaffLoan, 2, '.', ''),
            'pending_medical_insurance'     => number_format($pendingMedicalInsurance, 2, '.', ''),
            'pending_salary_advance'        => number_format($pendingSalaryAdvance, 2, '.', ''),
            'pending_psra'                  => number_format($pendingPsra, 2, '.', ''),
            'pending_bank_loan'             => number_format($pendingBankLoan, 2, '.', ''),
            'pending_approved_pension'      => number_format($pendingApprovedPension, 2, '.', ''),
            // 'pending_other_deduction'       => number_format($pendingOtherDeduction, 2, '.', ''),
            'garnishment'                   => number_format($garnishment, 2, '.', ''),
            'missing_goods'                 => number_format($missingGoods, 2, '.', ''),
            'damaged_goods'                 => number_format($damagedGoods, 2, '.', ''),
            'pending_garnishment'           => number_format($pendingGarnishment, 2, '.', ''),
            'pending_missing_goods'         => number_format($pendingMissingGoods, 2, '.', ''),
            'pending_damaged_goods'         => number_format($pendingDamagedGoods, 2, '.', ''),
        ];
    }

    protected function createPayrollDetails($payrollId, $userId, $attendanceDetails, $publicHolidays)
    {
        $regularWorkingHoursPerDay = 8;

        foreach ($attendanceDetails as $attendanceDate => $attendanceDetail) {
            $existingPayrollDetail = PayrollDetail::where('payroll_id', $payrollId)->where('guard_id', $userId)
                                                    ->where('date', $attendanceDate)->first();

            if (!$existingPayrollDetail) {
                $totalWorkedMinutes = 0;
                $regularMinutes = 0;
                $overtimeMinutes = 0;
                $publicHolidayMinutes = 0;

                foreach ($attendanceDetail as $attendanceForDay) {
                    $inTime = Carbon::parse($attendanceForDay['in_time']);
                    $outTime = Carbon::parse($attendanceForDay['out_time']);

                    $workedMinutes = $inTime->diffInMinutes($outTime);
                    $totalWorkedMinutes += $workedMinutes;
                    $guardTypeId = $attendanceForDay['guard_type_id'];
                }

                $isPublicHoliday = in_array($attendanceDate, $publicHolidays);

                if ($isPublicHoliday) {
                    $publicHolidayMinutes = $totalWorkedMinutes;
                } else {
                    if ($totalWorkedMinutes <= $regularWorkingHoursPerDay * 60) {
                        $regularMinutes = $totalWorkedMinutes;
                    } else {
                        $regularMinutes = $regularWorkingHoursPerDay * 60;
                        $overtimeMinutes = $totalWorkedMinutes - ($regularWorkingHoursPerDay * 60);
                    }
                }

                $regularHours = intdiv($regularMinutes, 60);
                $regularRemainingMinutes = $regularMinutes % 60;

                $overtimeHours = intdiv($overtimeMinutes, 60);
                $overtimeRemainingMinutes = $overtimeMinutes % 60;

                $publicHolidayHours = intdiv($publicHolidayMinutes, 60);
                $publicHolidayRemainingMinutes = $publicHolidayMinutes % 60;

                PayrollDetail::create([
                    'payroll_id' => $payrollId,
                    'guard_id' => $userId,
                    'guard_type_id' => $guardTypeId,
                    'date' => $attendanceDate,
                    'normal_hours' => $regularHours . '.' . str_pad($regularRemainingMinutes, 2, '0', STR_PAD_LEFT),
                    'overtime' => $overtimeHours . '.' . str_pad($overtimeRemainingMinutes, 2, '0', STR_PAD_LEFT),
                    'public_holiday' => $publicHolidayHours . '.' . str_pad($publicHolidayRemainingMinutes, 2, '0', STR_PAD_LEFT),
                ]);
            }
        }
    }
}
