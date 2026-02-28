<?php

namespace App\Filament\Widgets;

use App\Models\Term;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TermStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Términos', Term::query()->count())
                ->description('Total registrados')
                ->icon('heroicon-o-book-open'),
        ];
    }
}
