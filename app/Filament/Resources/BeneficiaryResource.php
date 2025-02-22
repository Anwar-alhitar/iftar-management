<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeneficiaryResource\Pages;
use App\Filament\Resources\BeneficiaryResource\RelationManagers;
use App\Models\Beneficiary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\DateFilter;
use Filament\Tables\Actions;
class BeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'المستفيدين';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                ->required(),
            Forms\Components\TextInput::make('id_number')
                ->unique('beneficiaries', 'id_number')
                ->required(),
            Forms\Components\Select::make('gender')
                ->options(['male' => 'ذكر', 'female' => 'أنثى'])
                ->default(auth()->user()->gender)
               // ->disabled(auth()->user()->role === 'employee' || auth()->user()->role==="admin")
                ->required(),
                Forms\Components\TextInput::make('serial_number')
                ->disabled()
                ->label('الرقم التسلسلي')
                ->visibleOn('view')
                ->formatStateUsing(function ($record) {
                    return $record->gender . '-' . str_pad($record->serial_number, 5, '0', STR_PAD_LEFT);
                })
                ->visibleOn('view'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('serial_number')
                ->label('الرقم التسلسلي')
                ->formatStateUsing(fn ($state, $record) => 
                    $record->gender === 'male' ? 'M-' : 'F-' . str_pad($state, 5, '0', STR_PAD_LEFT)
                )
                ->sortable()
                ->searchable(),
                
            TextColumn::make('full_name')
                ->label('الاسم الكامل')
                ->searchable()
                ->sortable(),
                
            TextColumn::make('id_number')
                ->label('رقم الهوية')
                ->searchable(),
                
            TextColumn::make('gender')
                ->label('الجنس')
                ->formatStateUsing(fn ($state) => $state === 'male' ? 'ذكر' : 'أنثى')
                ->badge()
                ->color(fn ($state) => $state === 'male' ? 'primary' : 'success'),
                
            TextColumn::make('meal_distributions_count')
                ->label('إجمالي الوجبات')
                ->counts('meal_distributions')
                ->sortable(),
                
            TextColumn::make('created_at')
                ->label('تاريخ التسجيل')
                ->dateTime('d/m/Y')
                ->sortable()
        ])
        ->filters([
            SelectFilter::make('gender')
                ->label('تصفية حسب الجنس')
                ->options([
                    'male' => 'ذكر',
                    'female' => 'أنثى'
                ]),
                
                Filament\Tables\Filters\DateFilter::make('created_at')
                ->label('تاريخ التسجيل')
        ])
        ->actions([
            Filament\Tables\Actions\ViewAction::make()->icon('heroicon-s-eye'),
            Filament\Tables\Actions\EditAction::make()->icon('heroicon-s-pencil'),
        ])
        ->bulkActions([
            Filament\Tables\Actions\DeleteBulkAction::make()->icon('heroicon-s-trash'),
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
            'index' => Pages\ListBeneficiaries::route('/'),
            'create' => Pages\CreateBeneficiary::route('/create'),
            'edit' => Pages\EditBeneficiary::route('/{record}/edit'),
        ];
    }
}
