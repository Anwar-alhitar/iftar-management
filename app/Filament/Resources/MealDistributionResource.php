<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MealDistributionResource\Pages;
use App\Filament\Resources\MealDistributionResource\RelationManagers;
use App\Models\MealDistribution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;
class MealDistributionResource extends Resource
{
    protected static ?string $model = MealDistribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'توزيع الوجبات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\BelongsToSelect::make('beneficiary_id')
                ->relationship('beneficiary', 'full_name')
                ->searchable()
                ->required()
                ->rules([
                    Rule::unique('meal_distributions')->where(function ($query) {
                        return $query->whereDate('distributed_at', now()->toDateString());
                    })
                ]),
            Forms\Components\DatePicker::make('distributed_at')
                ->default(now())
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMealDistributions::route('/'),
            'create' => Pages\CreateMealDistribution::route('/create'),
            'edit' => Pages\EditMealDistribution::route('/{record}/edit'),
        ];
    }
}
