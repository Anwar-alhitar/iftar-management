<?php

namespace App\Filament\Resources\MealDistributionResource\Pages;

use App\Filament\Resources\MealDistributionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMealDistribution extends CreateRecord
{
    protected static string $resource = MealDistributionResource::class;
 protected function afterCreate(): void
    {
        // إعادة تعيين حالة النموذج بحيث تُمسح بيانات المستفيد
        $this->form->fill([
            'beneficiary_id'   => null,
            'beneficiary_name' => null,
            // يمكنك إعادة تعيين حقول أخرى حسب الحاجة
        ]);
    }
}
