<?php

namespace App\Exports;
// namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceData implements FromCollection, WithHeadings
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
                'employee_name' => $item['employee_name'] ?? '', // Safe access with default value
                'absent' => $item['total_leaves'] ?? 0, // Ensure defaults
                'present' => $item['total_working_days'] ?? 0,
                'working_days' => $item['total_working_days'] ?? 0,
                'overtime' => $item['total_overtime'] ?? 0,
                'totalsalary' => $item['actual_salary'] ?? 0,
                'otsalary' => $item['overtime_salary'] ?? 0,
                'salaryadvance' => $item['salary_of_this_month'] ?? 0,
                'beforeadvance' => $item['salary_of_this_month'] ?? 0,

                'advance' => $item['advance'] ?? 0,
                'inhand' => ($item['salary_of_this_month'] ?? 0) - ($item['advance'] ?? 0), // Ensure calculations are safe
                'pf' => $item['salary_details']['pf_basic'] ?? 0,
                'esi' =>  $item['salary_details']['pf_basic'] ?? 0,
                'transfer' => $item['salary_of_this_month'] ?? 0,
                'cash' => $item['salary_of_this_month'] ?? 0,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Absent',
            'Present',
            'Working Days',
            'Overtime',
            'Salary',
            'OT Salary',
            'Total Salary',
            'Salary B. Advance',
            'Advance',
            'Salary In Hand',
            'PF',
            'ESI',
            'Transfer',
            'Cash',
        ];
    }
}

