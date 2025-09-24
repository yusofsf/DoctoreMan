<?php

namespace App\Filament\Resources;

use App\Enums\AdminStatus;
use App\Filament\Resources\AdminResource\Pages;
use App\Models\Admin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'ادمین ها';
    protected static ?string $modelLabel = 'ادمین';
    protected static ?string $pluralModelLabel = 'ادمین ها';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                    ->required()
                    ->label('کاربر'),

                Forms\Components\TextInput::make('description')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('توضیحات'),

                Forms\Components\Select::make('status')
                    ->required()
                    ->options(AdminStatus::getOptions())
                    ->label('وضعیت'),

                Forms\Components\TextInput::make('display_name')
                    ->default('مدیر')
                    ->label('نام نمایشی'),
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

                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->label('توضیحات')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn (AdminStatus $state): string => $state->getLabel())
                    ->sortable()
                    ->label('وضعیت'),

                Tables\Columns\TextColumn::make('display_name')
                    ->searchable()
                    ->sortable()
                    ->label('نام نمایشی'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AdminStatus::getOptions())
                    ->label('وضعیت'),
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'view' => Pages\ViewAdmin::route('/{record}'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
