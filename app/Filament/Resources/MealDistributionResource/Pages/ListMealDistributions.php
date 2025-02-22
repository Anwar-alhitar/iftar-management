<?php

namespace App\Filament\Resources\MealDistributionResource\Pages;

use App\Filament\Resources\MealDistributionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMealDistributions extends ListRecords
{
    protected static string $resource = MealDistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
