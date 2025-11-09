<?php

namespace App\Filament\Resources;

use App\Enums\DayOfWeek;
use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Rules\WithinDoctorWorkDays;
use App\Rules\WithinDoctorWorkHours;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Notifications\Notification;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'زمان بندی ها';
    protected static ?string $modelLabel = 'زمان بندی';
    protected static ?string $pluralModelLabel = 'زمان بندی ها';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('appointment_id')
                    ->relationship('appointment', 'id')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $appointment = Appointment::find($state);
                        if ($appointment && $appointment->patient) {
                            $set('user_id', $appointment->patient->user_id);
                        }
                    })
                    ->label('نوبت'),

                Forms\Components\Hidden::make('user_id'),

                Forms\Components\Select::make('day_of_week')
                    ->options(DayOfWeek::getOptions())
                    ->required()
                    ->label('روز هفته')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $appointmentId = $get('appointment_id');

                        if (!$appointmentId || !$state) {
                            return;
                        }

                        $appointment = Appointment::with('doctor')->find($appointmentId);

                        if (!$appointment || !$appointment->doctor) {
                            return;
                        }

                        $doctor = $appointment->doctor;
                        $workingDays = $doctor->working_days;

                        if (!$workingDays || empty($workingDays)) {
                            Notification::make()
                                ->danger()
                                ->title('خطا')
                                ->body('روزهای کاری دکتر تعریف نشده است.')
                                ->send();
                            return;
                        }

                        $selectedDay = $state instanceof DayOfWeek ? $state->value : $state;

                        if (!in_array($selectedDay, $workingDays)) {
                            $dayLabel = 'نامشخص';
                            if ($state instanceof DayOfWeek) {
                                $dayLabel = $state->getLabel();
                            } else {
                                try {
                                    $dayLabel = DayOfWeek::from($state)->getLabel();
                                } catch (\Exception $e) {
                                    $dayLabel = $state;
                                }
                            }

                            // تبدیل تمام روزهای کاری به فارسی
                            $workingDaysLabels = collect($workingDays)->map(function($day) {
                                try {
                                    return DayOfWeek::from($day)->getLabel();
                                } catch (\Exception $e) {
                                    return $day;
                                }
                            })->implode('، ');

                            Notification::make()
                                ->danger()
                                ->title('خطا در انتخاب روز')
                                ->body("روز {$dayLabel} در روزهای کاری دکتر نیست. روزهای کاری: {$workingDaysLabels}")
                                ->send();
                        } else {
                            // اگر روز معتبر است و start_time وجود دارد، end_time را مجدداً محاسبه کن
                            $startTime = $get('start_time');
                            if ($startTime) {
                                $appointment = Appointment::with('doctor')->find($get('appointment_id'));
                                if ($appointment && $appointment->doctor) {
                                    $doctor = $appointment->doctor;
                                    $selectedDay = $state instanceof DayOfWeek ? $state : DayOfWeek::from($state);
                                    $workingHours = $doctor->getWorkingHoursForDay($selectedDay);
                                    
                                    if ($workingHours['is_working'] && !empty($workingHours['start_time']) && !empty($workingHours['end_time'])) {
                                        try {
                                            $parseTime = function($time) {
                                                if (empty($time)) return null;
                                                if ($time instanceof Carbon) return $time->copy();
                                                $formats = ['H:i:s', 'H:i', 'H:i:s.u'];
                                                foreach ($formats as $format) {
                                                    try {
                                                        $parsed = Carbon::createFromFormat($format, $time);
                                                        if ($parsed) return $parsed;
                                                    } catch (\Exception $e) {
                                                        continue;
                                                    }
                                                }
                                                return Carbon::parse($time);
                                            };

                                            $startTimeObj = $parseTime($startTime);
                                            $workEnd = $parseTime($workingHours['end_time']);
                                            
                                            if ($startTimeObj && $workEnd) {
                                                $baseDate = Carbon::today();
                                                $startTimeObj->setDate($baseDate->year, $baseDate->month, $baseDate->day);
                                                $workEnd->setDate($baseDate->year, $baseDate->month, $baseDate->day);
                                                
                                                $consultationDuration = $doctor->consultation_duration ?? 30;
                                                $calculatedEndTime = $startTimeObj->copy()->addMinutes($consultationDuration);
                                                
                                                if ($calculatedEndTime->lte($workEnd)) {
                                                    $set('end_time', $calculatedEndTime->format('H:i:s'));
                                                }
                                            }
                                        } catch (\Exception $e) {
                                            // در صورت خطا، کاری انجام نده
                                        }
                                    }
                                }
                            }
                        }
                    })
                    ->rules([
                        function ($get) {
                            return new WithinDoctorWorkDays($get('appointment_id'));
                        },
                    ]),

                Forms\Components\TimePicker::make('start_time')
                    ->required()
                    ->label('زمان شروع')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $appointmentId = $get('appointment_id');
                        $dayOfWeek = $get('day_of_week');

                        // اگر start_time خالی شد، end_time را هم خالی کن
                        if (!$appointmentId || !$state) {
                            $set('end_time', null);
                            return;
                        }

                        if (!$dayOfWeek) {
                            Notification::make()
                                ->warning()
                                ->title('توجه')
                                ->body('لطفاً ابتدا روز هفته را انتخاب کنید.')
                                ->send();
                            return;
                        }

                        $appointment = Appointment::with('doctor')->find($appointmentId);

                        if (!$appointment || !$appointment->doctor) {
                            return;
                        }

                        $doctor = $appointment->doctor;

                        // تبدیل dayOfWeek به enum
                        $selectedDay = $dayOfWeek instanceof DayOfWeek 
                            ? $dayOfWeek 
                            : (is_string($dayOfWeek) ? DayOfWeek::from($dayOfWeek) : null);

                        if (!$selectedDay) {
                            return;
                        }

                        $workingHours = $doctor->getWorkingHoursForDay($selectedDay);

                        // بررسی اینکه آیا این روز یک روز کاری است
                        if (!$workingHours['is_working']) {
                            Notification::make()
                                ->danger()
                                ->title('خطا')
                                ->body('این روز در روزهای کاری دکتر نیست.')
                                ->send();
                            $set('start_time', null);
                            return;
                        }

                        // بررسی اینکه ساعات کاری برای این روز تعریف شده است
                        if (empty($workingHours['start_time']) || empty($workingHours['end_time'])) {
                            Notification::make()
                                ->danger()
                                ->title('خطا')
                                ->body('ساعات کاری دکتر برای این روز تعریف نشده است.')
                                ->send();
                            $set('start_time', null);
                            return;
                        }

                        // تبدیل زمان‌ها به Carbon برای مقایسه صحیح
                        try {
                            $parseTime = function($time) {
                                if (empty($time)) {
                                    return null;
                                }
                                if ($time instanceof Carbon) {
                                    return $time->copy();
                                }
                                $formats = ['H:i:s', 'H:i', 'H:i:s.u'];
                                foreach ($formats as $format) {
                                    try {
                                        $parsed = Carbon::createFromFormat($format, $time);
                                        if ($parsed) {
                                            return $parsed;
                                        }
                                    } catch (\Exception $e) {
                                        continue;
                                    }
                                }
                                return Carbon::parse($time);
                            };

                            $startTime = $parseTime($state);
                            $workStart = $parseTime($workingHours['start_time']);
                            $workEnd = $parseTime($workingHours['end_time']);

                            if (!$startTime || !$workStart || !$workEnd) {
                                throw new \Exception('فرمت زمان نامعتبر است.');
                            }

                            $baseDate = Carbon::today();
                            $startTime->setDate($baseDate->year, $baseDate->month, $baseDate->day);
                            $workStart->setDate($baseDate->year, $baseDate->month, $baseDate->day);
                            $workEnd->setDate($baseDate->year, $baseDate->month, $baseDate->day);

                            $startTime->second(0)->microsecond(0);
                            $workStart->second(0)->microsecond(0);
                            $workEnd->second(0)->microsecond(0);

                            // بررسی اینکه زمان شروع در بازه ساعات کاری است یا نه
                            if ($startTime->lt($workStart) || $startTime->gt($workEnd)) {
                                Notification::make()
                                    ->danger()
                                    ->title('خطا در زمان شروع')
                                    ->body("زمان شروع باید بین ساعات کاری دکتر ({$workStart->format('H:i')} تا {$workEnd->format('H:i')}) باشد.")
                                    ->send();
                                $set('start_time', null);
                                $set('end_time', null);
                                return;
                            }

                            $consultationDuration = $doctor->consultation_duration ?? 30; // پیش‌فرض 30 دقیقه
                            $calculatedEndTime = $startTime->copy()->addMinutes($consultationDuration);

                            // بررسی اینکه زمان پایان از ساعت کاری خارج نشود
                            if ($calculatedEndTime->gt($workEnd)) {
                                Notification::make()
                                    ->danger()
                                    ->title('خطا در زمان')
                                    ->body("با مدت زمان جلسه ({$consultationDuration} دقیقه)، زمان پایان ({$calculatedEndTime->format('H:i')}) از ساعت کاری دکتر ({$workEnd->format('H:i')}) تجاوز می‌کند.")
                                    ->send();
                                $set('start_time', null);
                                $set('end_time', null);
                                return;
                            }

                            // تنظیم زمان پایان
                            $set('end_time', $calculatedEndTime->format('H:i:s'));
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('خطا')
                                ->body('فرمت زمان نامعتبر است.')
                                ->send();
                            $set('start_time', null);
                            $set('end_time', null);
                        }
                    })
                    ->rules([
                        function ($get) {
                            return new WithinDoctorWorkHours($get('appointment_id'), $get('day_of_week'));
                        },
                    ]),

                Forms\Components\TimePicker::make('end_time')
                    ->label('زمان پایان')
                    ->required()
                    ->readonly()
                    ->dehydrated()
                    ->helperText('زمان پایان به صورت خودکار بر اساس مدت زمان جلسه محاسبه می‌شود')
                    ->reactive(),
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
                    ->sortable()
                    ->searchable()
                    ->placeholder('رزور نشده')
                    ->label('کاربر'),

                Tables\Columns\TextColumn::make('day_of_week')
                    ->formatStateUsing(fn (DayOfWeek $state): string => $state->getLabel())
                    ->sortable()
                    ->searchable()
                    ->label('روز هفته'),

                Tables\Columns\TextColumn::make('start_time')
                    ->time()
                    ->sortable()
                    ->label('زمان شروع'),

                Tables\Columns\TextColumn::make('end_time')
                    ->time()
                    ->sortable()
                    ->label('زمان پایان'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('day_of_week')
                    ->options(DayOfWeek::getOptions())
                    ->label('روز هفته'),
            ])
            ->recordUrl(fn (Schedule $record) => static::getUrl('view', ['record' => $record]))
            ->actionsPosition(ActionsPosition::AfterCells)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('مشاهده'),
                Tables\Actions\EditAction::make()
                    ->label('ویرایش'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'view' => Pages\ViewSchedule::route('/{record}'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}