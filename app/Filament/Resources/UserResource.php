<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'کاربر ها';

    protected static ?string $modelLabel = 'کاربر';
    
    protected static ?string $pluralModelLabel = 'کاربر ها';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_name')
                    ->required()
                    ->label('نام کاربری'),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->label('نام کوچک'),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->label('نام خانوادگی'),
                Forms\Components\Select::make('role')
                    ->options(UserRole::getOptions())
                    ->required()
                    ->label('نقش'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->label('ایمیل'),
                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->label('تایید ایمیل'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->label('رمز عبور'),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->label('آدرس')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->formatStateUsing(fn(UserRole $state): string => $state->getLabel())
                    ->searchable()
                    ->label('role'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                ->options(UserRole::getOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('ویرایش'),
                Tables\Actions\ViewAction::make()
                    ->label('مشاهده'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
