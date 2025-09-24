<?php

namespace App\Filament\Resources;

use App\Enums\Gender;
use App\Filament\Resources\PatientResource\Pages;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'بیمار ها';
    protected static ?string $modelLabel = 'بیمار';
    protected static ?string $pluralModelLabel = 'بیمار ها';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                    ->required()
                    ->label('کاربر'),

                Forms\Components\Select::make('gender')
                    ->options(Gender::getOptions())
                    ->required()
                    ->label('جنسیت'),

                Forms\Components\TextInput::make('phone_number')
                    ->required()
                    ->label('شماره موبایل'),

                Forms\Components\DatePicker::make('birth_date')
                    ->required()
                    ->label('تاریخ تولد'),
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

                Tables\Columns\TextColumn::make('gender')
                    ->sortable()
                    ->label('جنسیت'),

                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->label('شماره موبایل'),

                Tables\Columns\TextColumn::make('birth_date')
                    ->date('Y-m-d')
                    ->sortable()
                    ->label('تاریخ تولد'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->options(Gender::getOptions())
                    ->label('جنسیت'),
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'view' => Pages\ViewPatient::route('/{record}'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
