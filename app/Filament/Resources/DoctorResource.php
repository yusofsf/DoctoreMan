<?php

namespace App\Filament\Resources;

use App\Enums\DayOfWeek;
use App\Filament\Resources\DoctorResource\Pages;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'دکتر ها';
    protected static ?string $modelLabel = 'دکتر';
    protected static ?string $pluralModelLabel = 'دکتر ها';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'first_name',
                        modifyQueryUsing: fn (Builder $query) =>
                        $query->where('role', 'دکتر'),
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->first_name . ' ' . $record->last_name
                    )
                    ->required()
                    ->label('دکتر'),

                Forms\Components\TextInput::make('medical_code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('کد نظام پزشکی'),

                Forms\Components\TextInput::make('city')
                    ->required()
                    ->label('شهر'),

                Forms\Components\TextInput::make('speciality')
                    ->required()
                    ->label('تخصص'),

                Forms\Components\Textarea::make('bio')
                    ->label('بیوگرافی')
                    ->rows(3),

                Forms\Components\TextInput::make('consultation_fee')
                    ->numeric()
                    ->prefix('ریال')
                    ->label('هزینه ویزیت'),

                Forms\Components\TextInput::make('consultation_duration')
                    ->numeric()
                    ->suffix('دقیقه')
                    ->default(15)
                    ->label('مدت زمان ویزیت'),

                Forms\Components\TimePicker::make('start_time')
                    ->default('08:00:00')
                    ->prefix('ساعت')
                    ->label('شروع ساعت کاری'),

                Forms\Components\TimePicker::make('end_time')
                    ->default('08:00:00')
                    ->prefix('ساعت')
                    ->label('پایان ساعت کاری'),

                Forms\Components\Select::make('working_days')
                    ->options(DayOfWeek::getOptions())
                    ->multiple()
                    ->label('روز های کاری')
                    ->default([
                        DayOfWeek::SATURDAY->value,
                        DayOfWeek::SUNDAY->value,
                    ])
                    ->helperText('روز‌هایی که دکتر در دسترس است را انتخاب کنید')

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.first_name')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->user) {
                            return $record->user->first_name . ' ' . $record->user->last_name;
                        }
                        return 'کاربر یافت نشد';
                    })
                    ->searchable()
                    ->sortable()
                    ->label('نام'),

                Tables\Columns\TextColumn::make('medical_code')
                    ->searchable()
                    ->label('کد نظام پزشکی')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->label('شهر'),

                Tables\Columns\TextColumn::make('speciality')
                    ->searchable()
                    ->sortable()
                    ->label('تخصص'),

                Tables\Columns\TextColumn::make('bio')
                    ->label('بیوگرافی')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('consultation_fee')
                    ->money('IRR')
                    ->label('هزینه ویزیت'),

                Tables\Columns\TextColumn::make('appointments_count')
                    ->counts('appointments')
                    ->label('تعداد نوبت‌ها')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('consultation_duration')
                    ->sortable()
                    ->label('مدت زمان ویزیت'),

                Tables\Columns\TextColumn::make('working_days')
                    ->label('روزهای کاری')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'تعریف نشده';
                        }


                        if (is_string($state)) {
                            $state = explode(',', $state);
                        }

                        $dayLabels = [];
                        foreach ($state as $dayValue) {
                            $dayValue = strtoupper(trim($dayValue));
                            $day = DayOfWeek::fromName($dayValue);
                            if ($day) {
                                $dayLabels[] = $day->getLabel();
                            }
                        }

                        return !empty($dayLabels) ? implode('، ', $dayLabels) : 'تعریف نشده';
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('start_time')
                    ->sortable()
                    ->time('H:i')
                    ->label('شروع ساعت کاری'),

                Tables\Columns\TextColumn::make('end_time')
                    ->sortable()
                    ->time('H:i')
                    ->label('پایان ساعت کاری'),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ویرایش'),
                Tables\Actions\ViewAction::make()
                    ->label('مشاهده'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'view' => Pages\ViewDoctor::route('/{record}'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
}
