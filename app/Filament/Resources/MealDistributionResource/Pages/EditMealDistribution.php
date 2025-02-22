<?php

namespace App\Filament\Resources\MealDistributionResource\Pages;

use App\Filament\Resources\MealDistributionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMealDistribution extends EditRecord
{
    protected static string $resource = MealDistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
