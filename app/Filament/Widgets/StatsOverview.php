<?php

namespace App\Filament\Widgets;

use App\Models\Clients;
use App\Models\Projects;
use App\Models\Transactions;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        $client = Clients::count();

        $project = Projects::where('is_done', 'done')->count();

        $kas = Transactions::where('categorie_finance_id', 2)->sum('total');

        $pajak = Transactions::where('categorie_finance_id', 3)->sum('total');

        return [
            Stat::make('Total Client', $client),
            Stat::make('Total Project', $project),
            Stat::make('Total Kas', 'Rp.' . number_format($kas, 0, ',', '.')),
            Stat::make('Total Pajak', 'Rp.' . number_format($pajak, 0, ',', '.')),
        ];
    }
}
