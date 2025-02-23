<?php

namespace App\Filament\Resources;

use App\Models\MealDistribution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use App\Filament\Resources\MealDistributionResource\pages;
class MealDistributionResource extends Resource
{
    protected static ?string $model = MealDistribution::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'توزيع الوجبات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('beneficiary_id')
                    ->relationship(
                        name: 'beneficiary',
                        titleAttribute: 'full_name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('gender', auth()->user()->gender)
                    )
                    ->label('المستفيد')
                    ->searchable()
                    ->required()
                    ->rules([
                        Rule::unique('meal_distributions')->where(function ($query) {
                            return $query->whereDate('distributed_at', now()->toDateString());
                        })
                    ]),

                Forms\Components\DateTimePicker::make('distributed_at')
                    ->label('تاريخ التوزيع')
                    ->default(now())
                    ->required()
                    ->disabled()
                    ->displayFormat('d/m/Y H:i'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('beneficiary.serial_number')
                    ->label('الرقم التسلسلي')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record->beneficiary) return '';

                        $gender = $record->beneficiary->gender === 'male' ? 'M-' : 'F-';
                        return $gender . str_pad($state, 5, '0', STR_PAD_LEFT);
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('beneficiary.full_name')
                    ->label('اسم المستفيد')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('الموظف المسؤول')
                    ->searchable(),

                Tables\Columns\TextColumn::make('distributed_at')
                    ->label('تاريخ التوزيع')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label('الموظف')
                    ->relationship('user', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->icon('heroicon-s-pencil'),
                Tables\Actions\DeleteAction::make()->icon('heroicon-s-trash'),
            ])
            ->defaultSort('distributed_at', 'desc');
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
