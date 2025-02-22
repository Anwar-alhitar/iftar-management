<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions;
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'الموظفين';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required(),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required(),
                Forms\Components\TextInput::make('password')
                ->password()
                ->required(),
            Forms\Components\Select::make('gender')
                ->options(['male' => 'ذكر', 'female' => 'أنثى'])
                ->required(),
            Forms\Components\Select::make('role')
                ->options(['admin' => 'مدير', 'employee' => 'موظف'])
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('name')
                ->label('الاسم')
                ->searchable()
                ->sortable(),
                
            TextColumn::make('email')
                ->label('البريد الإلكتروني')
                ->searchable(),
                
            TextColumn::make('role')
                ->label('الدور')
                ->formatStateUsing(fn ($state) => $state === 'admin' ? 'مدير' : 'موظف')
                ->badge()
                ->color(fn ($state) => $state === 'admin' ? 'danger' : 'primary'),
                
            TextColumn::make('gender')
                ->label('الجنس المسؤول')
                ->formatStateUsing(fn ($state) => $state === 'male' ? 'ذكر' : 'أنثى')
                ->badge()
                ->color(fn ($state) => $state === 'male' ? 'primary' : 'success'),
                
            TextColumn::make('created_at')
                ->label('تاريخ التسجيل')
                ->dateTime('d/m/Y')
                ->sortable()
        ])
        ->filters([
            SelectFilter::make('role')
                ->label('الدور')
                ->options([
                    'admin' => 'مدير',
                    'employee' => 'موظف'
                ]),
                
            SelectFilter::make('gender')
                ->label('الجنس')
                ->options([
                    'male' => 'ذكر',
                    'female' => 'أنثى'
                ])
        ])
        ->actions([
            Filament\Tables\Actions\EditAction::make()->icon('heroicon-s-pencil'),
            Filament\Tables\Actions\DeleteAction::make()->icon('heroicon-s-trash'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
