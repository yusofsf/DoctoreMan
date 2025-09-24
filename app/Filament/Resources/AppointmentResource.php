<?php

namespace App\Filament\Resources;

use App\Enums\AppointmentStatus;
use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentResource extends Resource
{

    protected static ?string $model = Appointment::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'نوبت ‌ها';
    protected static ?string $modelLabel = 'نوبت';
    protected static ?string $pluralModelLabel = 'نوبت ها';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات نوبت')
                    ->schema([
                        Forms\Components\Select::make('doctor_id')
                            ->relationship(
                                name: 'doctor',
                                titleAttribute: 'id',
                                modifyQueryUsing: fn (Builder $query) => $query->whereHas('user', fn ($q) => $q->where('role', 'دکتر')),
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->first_name . ' ' . $record->user?->last_name)
                            ->required()
                            ->label('دکتر'),

                        Forms\Components\Select::make('patient_id')
                            ->relationship(
                                name: 'patient',
                                titleAttribute: 'id',
                                modifyQueryUsing: fn (Builder $query) => $query->whereHas('user', fn ($q) => $q->where('role', 'بیمار')),
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->first_name . ' ' . $record->user?->last_name)
                            ->required()
                            ->label('بیمار'),

                    ])->columns(2),

                Forms\Components\Section::make('زمان‌بندی')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->label('تاریخ')
                            ->rule(fn (Get $get) => function (string $attribute, $value, Closure $fail) use ($get) {
                                $doctorId = $get('doctor_id');

                                if (!$doctorId || !$value) {
                                    return;
                                }

                                $doctor = Doctor::find($doctorId);
                                if (!$doctor) {
                                    return;
                                }

                                try {
                                    $dayName = strtolower(Carbon::parse($value)->englishDayOfWeek);
                                } catch (\Throwable) {
                                    return;
                                }

                                $workingDays = $doctor->working_days ?? [];
                                $isWorking = false;

                                if (is_array($workingDays)) {
                                    if (array_key_exists($dayName, $workingDays)) {
                                        $dayConfig = $workingDays[$dayName];
                                        $isWorking = is_array($dayConfig)
                                            ? (bool)($dayConfig['is_working'] ?? false)
                                            : (bool)$dayConfig;
                                    } else {
                                        $isWorking = in_array($dayName, $workingDays, true);
                                    }
                                }

                                if (!$isWorking) {
                                    $fail('تاریخ انتخاب‌شده در روزهای کاری دکتر نیست.');
                                }
                            }),

                    ])->columns(1),

                Forms\Components\Section::make('وضعیت')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(AppointmentStatus::getOptions())
                            ->required()
                            ->label('وضعیت'),
                    ])->columns(2),

                Forms\Components\Textarea::make('notes')
                    ->label('یادداشت')
                    ->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('row_number')
                    ->label('ردیف')
                    ->rowIndex(),

                Tables\Columns\TextColumn::make('patient.user.first_name')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->patient && $record->patient->user) {
                            return $record->patient->user->first_name . ' ' . $record->patient->user->last_name;
                        }
                        return 'بیمار یافت نشد';
                    })
                    ->searchable()
                    ->placeholder('مشخص نشده')
                    ->label('بیمار'),

                Tables\Columns\TextColumn::make('doctor.user.first_name')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->doctor && $record->doctor->user ) {
                            return $record->doctor->user->first_name . ' ' . $record->doctor->user->last_name;
                        }
                        return 'دکتر یافت نشد';
                    })
                    ->searchable()
                    ->label('دکتر'),

                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->label('تاریخ'),

                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->label('وضعیت'),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AppointmentStatus::getOptions())
                    ->label('وضعیت'),

                Tables\Filters\SelectFilter::make('doctor')
                    ->relationship('doctor.user', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                    ->label('دکتر'),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('از تاریخ'),
                        Forms\Components\DatePicker::make('until')
                            ->label('تا تاریخ'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('date', '<=', $data['until']));
                    }),
            ])
            ->defaultSort('date', 'desc')
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'view' => Pages\ViewAppointment::route('/{record}'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
