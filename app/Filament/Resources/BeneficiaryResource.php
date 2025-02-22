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
                //->disabled()
                ->readOnly()
                ->value(static::creating(function ($beneficiary) {
                    $maxSerial = Beneficiary::where('gender', $beneficiary->gender)
                        ->max('serial_number');
        
                    $beneficiary->serial_number = ($maxSerial ?? 0) + 1;
                }) )
                ->visibleOn('view'),
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
            'index' => Pages\ListBeneficiaries::route('/'),
            'create' => Pages\CreateBeneficiary::route('/create'),
            'edit' => Pages\EditBeneficiary::route('/{record}/edit'),
        ];
    }
}
