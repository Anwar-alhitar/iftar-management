<?php

namespace App\Filament\Resources;


use Alkoumi\LaravelHijriDate\Hijri;
use App\Models\Beneficiary;
use App\Models\MealDistribution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use App\Filament\Resources\MealDistributionResource\Pages;
use Filament\Notifications\Notification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MealDistributionResource extends Resource
{
    protected static ?string $model = MealDistribution::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'توزيع الوجبات';
    protected static ?string $pluralModelLabel = ' الوجبات الموزعة';
    protected static ?string $modelLabel = 'صرف وجبة';
    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count();
    }

    public static function form(Form $form): Form
    {
        return $form
           ->schema([

            Forms\Components\FileUpload::make('qr_code')
            ->label('مسح QR Code')
            ->reactive()
            ->afterStateUpdated(function ($state, callable $set) {
                if ($state) {
                    $qrContent = QrCode::reader()->decode($state);
                    $set('serial_number', $qrContent);
                }
            })
            ->disk('local')
            ->directory('qr-scans')
            ->preserveFilenames()
            ->acceptedFileTypes(['image/png', 'image/jpeg']),
        Forms\Components\TextInput::make('serial_number')
            ->label('الرقم التسلسلي')
            ->numeric()
            ->reactive()
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $beneficiary = Beneficiary::where('serial_number', $state)->first();

                if ($beneficiary) {
                    if (self::checkMealStatus($beneficiary->id)) {
                        Notification::make()
                            ->title('⚠️ تنبيه: هذا المستفيد قد استلم وجبة اليوم!')
                            ->success()
                            ->send();

                            $set('beneficiary_id', $beneficiary->id);
                            $set('beneficiary_name', $beneficiary->full_name);
                        $set('serial_number', null);
                        return;
                    }

                    $set('beneficiary_id', $beneficiary->id);
                    $set('beneficiary_name', $beneficiary->full_name);
                } else {
                    Notification::make()
                            ->title('⚠️ غير موجود')
                            ->success()
                            ->send();
                    //$set('beneficiary_id', null);
                  //  $set('beneficiary_name', null);
                    $set('serial_number', null);
                }
            })
            ->required()
        ->dehydrated(false), // لا يتم إرسال serial_search إلى قاعدة البيانات

    Forms\Components\Grid::make()
        ->schema([
            Forms\Components\Select::make('beneficiary_id')
                ->relationship(
                    name: 'beneficiary',
                    titleAttribute: 'full_name'

                )
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('meal_status', self::checkMealStatus($state));
                })
                ->hidden(fn (callable $get) => $get('beneficiary_id'))
                ->rules([
                    Rule::unique('meal_distributions')->where(function ($query) {
                        return $query->whereDate('distributed_at', now()->toDateString());
                    })
                ])
                ->getSearchResultsUsing(function (string $search) {
                    return Beneficiary::where('full_name', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->limit(50)
                        ->pluck('full_name', 'id');
                })
                ->getOptionLabelUsing(fn ($value): ?string => Beneficiary::find($value)?->full_name),

            Forms\Components\TextInput::make('beneficiary_name')
                ->label('اسم المستفيد')
                ->disabled()
                ->dehydrated()
                ->visible(fn (callable $get) => $get('beneficiary_id'))
        ])
        ->columns(2),

    // حقل جديد للإشعار
    // Forms\Components\View::make('filament.components.meal-status-alert')
    // ->extraAttributes(['class' => 'filament-meal-alert'])
    // ->columnSpanFull()
    // ->viewData([
    //     'status' => self::checkMealStatus(optional(auth()->user())->id)

    // ]),

    Forms\Components\DateTimePicker::make('distributed_at')
        ->label('تاريخ التوزيع')
        ->default(now())
        ->required()
        ->disabled()
        ->dehydrated(true) // ضمان تضمين الحقل في البيانات حتى وإن كان معطلاً
        ->displayFormat('d/m/Y H:i'),

    Forms\Components\TextInput::make('hijri_date')
        ->label('التاريخ الهجري')
        ->disabled()
        ->default(fn() => Hijri::date(now())),

    // حقل مخفي لـ beneficiary_id لضمان تمريره في الحفظ
    Forms\Components\Hidden::make('beneficiary_id')
        ->required()
        ->rule('exists:beneficiaries,id')
        ->validationMessages([
            'required' => 'يجب تحديد مستفيد قبل الحفظ.',
            'exists'   => 'المستفيد المحدد غير موجود في قاعدة البيانات.',
        ]),

    // حقل مخفي لـ user_id لتعيين المستخدم الحالي (أو null إذا لم يكن موجوداً)
    Forms\Components\Hidden::make('user_id')
        ->default(fn() => auth()->id() ?? null),

    ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('beneficiary.serial_number')
                    ->label('الرقم التسلسلي')
                    ->formatStateUsing(fn ($state) => str_pad($state, 5, '0', STR_PAD_LEFT))
                    ->sortable(),

                Tables\Columns\TextColumn::make('beneficiary.full_name')
                    ->label('اسم المستفيد')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('الموظف المسؤول')
                    ->searchable(),

                Tables\Columns\TextColumn::make('distributed_at')
                    ->label('التاريخ الميلادي')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('hijri_date')
                    ->label('التاريخ الهجري')
                    ->formatStateUsing(function ($state) {
                        return Hijri::date($state);
                    })
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label('الموظف')
                    ->relationship('user', 'name'),

                Tables\Filters\Filter::make('hijri_date')
                    ->form([
                        Forms\Components\DatePicker::make('hijri_date')
                            ->label('بحث بالتاريخ الهجري')
                            ->displayFormat('d F Y')
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['hijri_date']) {
                            $hijriDate = Hijri::date($data['hijri_date']);
                            $query->where('hijri_date', $hijriDate);
                        }
                    })
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

    // دالة مساعدة للتحقق من حالة الوجبة
    private static function checkMealStatus($beneficiaryId): bool
    {
        if (!$beneficiaryId) return false;

        $todayGregorian = now()->format('Y-m-d');
        $todayHijri = Hijri::date(now());

        return MealDistribution::where('beneficiary_id', $beneficiaryId)
            ->where(function ($query) use ($todayGregorian, $todayHijri) {
                $query->whereDate('distributed_at', $todayGregorian)
                    ->orWhere('hijri_date', $todayHijri);
            })
            ->exists();
    }


}
