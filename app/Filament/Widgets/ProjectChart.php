<?php

namespace App\Filament\Widgets;

use App\Models\Projects;
use Filament\Widgets\ChartWidget;

class ProjectChart extends ChartWidget
{
    protected static ?string $heading = 'Project Completed Per Month';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $currentYear = date('Y');       
        $dataProjectsRaw = Projects::selectRaw('COUNT(*) as count, MONTH(start_date) as month')
            ->where('is_done', 'Done')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        //memasukkan data ke array sesuai labels
        $dataProjects = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataProjects[] = $dataProjectsRaw[$i] ?? 0;
        }

        return [
            'datasets' => [
            [
                'label' => 'Projects',
                'data' => $dataProjects,                
            ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
