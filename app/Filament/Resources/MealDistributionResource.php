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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

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
            TextColumn::make('beneficiary.serial_number')
                ->label('الرقم التسلسلي')
                ->formatStateUsing(fn ($state, $record) => 
                    $record->beneficiary->gender === 'male' ? 'M-' : 'F-' . str_pad($state, 5, '0', STR_PAD_LEFT)
                )
                ->sortable(),
                
            TextColumn::make('beneficiary.full_name')
                ->label('اسم المستفيد')
                ->searchable()
                ->sortable(),
                
            TextColumn::make('user.name')
                ->label('الموظف المسؤول')
                ->searchable()
                ->sortable(),
                
            TextColumn::make('distributed_at')
                ->label('تاريخ التوزيع')
                ->dateTime('d/m/Y H:i')
                ->sortable(),
                
            TextColumn::make('created_at')
                ->label('وقت التسجيل')
                ->since()
                ->sortable()
        ])
        ->filters([
            SelectFilter::make('user')
                ->label('الموظف')
                ->relationship('user', 'name'),
                
                Filament\Tables\Filters\DateFilter::make('distributed_at')
                ->label('تاريخ التوزيع')
        ])
        ->actions([
            Filament\Tables\Actions\EditAction::make()->icon('heroicon-s-pencil'),
            Filament\Tables\Actions\DeleteAction::make()->icon('heroicon-s-trash'),
        ])
        ->defaultSort('distributed_at', 'desc');
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
