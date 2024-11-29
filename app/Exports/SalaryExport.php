<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalaryExport implements FromCollection, WithHeadings
{
    protected $attendanceData;

    public function __construct(array $attendanceData)
    {
        $this->attendanceData = $attendanceData;
    }

    public function collection()
    {
        return collect($this->attendanceData)->map(function ($item) {
            return [
                'employee_name' => $item['employee_name'] ?? '', // Safe access
                'year' => $item['year'] ?? '', // Ensure defaults
                'month' => $item['month'] ?? '',
                'working_days' => $item['working_days'] ?? 0,
                'basic' => $item['basic'] ?? 0,
                'pf_basic' => $item['pf_basic'] ?? 0,
                'hra' => $item['hra'] ?? 0,
                'conveyance' => $item['conveyance'] ?? 0,
                'other_allowance' => $item['other_allowance'] ?? 0,
                'advance' => $item['advance'] ?? 0,
                'rate_of_pay' => $item['rate_of_pay'] ?? 0,
                'epf_employee' => $item['epf_employee'] ?? 0,
                'epf_employer' => $item['epf_employer'] ?? 0,
                'eps_employer' => $item['eps_employer'] ?? 0,
                'esi_employee' => $item['esi_employee'] ?? 0,
                'esi_employer' => $item['esi_employer'] ?? 0,
                'lwf_employer' => $item['lwf_employer'] ?? 0,
                'lwf_employee' => $item['lwf_employee'] ?? 0,
                'total_deductions' => $item['total_deductions'] ?? 0,
                'net_payable' => $item['net_payable'] ?? 0,
                'actual_salary'=> $item['actual_salary'] ?? 0,
                'cash'=> $item['cash'] ?? 0,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Year',
            'Month',
            'Working Days',
            'Basic',
            'PF Basic',
            'HRA',
            'Conveyance',
            'Other Allowance',
            'Advance',
            'Rate of Pay',
            'EPF (Employee)',
            'EPF (Employer)',
            'EPS (Employer)',
            'ESI (Employee)',
            'ESI (Employer)',
            'LWF EMPLOYER',
            'LWF EMPLOYEE',
            'Total Deductions',
            'Net Payable',
            'Actual Salary',
            'Cash'
        ];
    }
}
