<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeneficiaryResource\Pages;
use App\Models\Beneficiary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;

class BeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'المستفيدين';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                    ->label('الاسم الكامل')
                    ->required(),

                Forms\Components\TextInput::make('id_number')
                    ->label('رقم الهوية')
                    ->unique('beneficiaries', 'id_number')
                    ->required(),

                Forms\Components\Select::make('gender')
                    ->label('الجنس')
                    ->options(['male' => 'ذكر', 'female' => 'أنثى'])
                    ->default(auth()->user()->gender ?? 'male') // إضافة قيمة افتراضية
                    ->required(),

                Forms\Components\TextInput::make('serial_number')
                    ->label('الرقم التسلسلي')
                    ->disabled()
                    ->visibleOn('view')
                    ->formatStateUsing(fn ($state) => str_pad($state, 5, '0', STR_PAD_LEFT)),
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

                TextColumn::make('serial_number')
                    ->label('الرقم التسلسلي')
                    ->formatStateUsing(function ($state, $record) {
                        // إضافة تحقق إضافي
                        if (!$record) return '';

                        $genderPrefix = $record->gender === 'male' ? 'M-' : 'F-';
                        return $genderPrefix . str_pad($state, 5, '0', STR_PAD_LEFT);
                    })
                    ->sortable()
                    ->searchable(),

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
                    ])
            ])
            ->actions([
                ViewAction::make()->icon('heroicon-s-eye'),
                EditAction::make()->icon('heroicon-s-pencil'),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->icon('heroicon-s-trash'),
            ]);
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
