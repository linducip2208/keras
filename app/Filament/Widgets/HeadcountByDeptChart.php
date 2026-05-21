<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Widgets\ChartWidget;

class HeadcountByDeptChart extends ChartWidget
{
    protected ?string $heading = 'Headcount per Departemen';

    protected function getData(): array
    {
        $data = Employee::query()
            ->whereIn('employment_status', ['active', 'probation'])
            ->join('departments', 'departments.id', '=', 'employees.department_id')
            ->selectRaw('departments.name as dept, COUNT(*) as count')
            ->groupBy('departments.name')
            ->orderByDesc('count')
            ->get();

        return [
            'datasets' => [[
                'label' => 'Karyawan',
                'data' => $data->pluck('count')->all(),
                'backgroundColor' => [
                    '#f97316', '#3b82f6', '#16a34a', '#dc2626',
                    '#9333ea', '#0891b2', '#ca8a04', '#db2777',
                ],
            ]],
            'labels' => $data->pluck('dept')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
