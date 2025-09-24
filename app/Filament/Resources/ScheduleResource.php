<?php

namespace App\Filament\Resources;

use App\Enums\DayOfWeek;
use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Appointment;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;

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
                    ->label('روز هفته'),

                Forms\Components\TimePicker::make('start_time')
                    ->required()
                    ->label('زمان شروع'),
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
