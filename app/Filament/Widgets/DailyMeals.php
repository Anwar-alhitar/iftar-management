<?php

namespace App\Filament\Widgets;

use App\Models\MealDistribution;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\Widget;

use Filament\Widgets\StatsOverviewWidget\Stat;
class DailyMeals extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('الوجبات اليومية', MealDistribution::whereDate('distributed_at', today())->count())
                ->icon('heroicon-s-truck'),
        ];
    }
}
