<?php

namespace App\Filament\Resources;

use App\Enums\StaffRole;
use App\Filament\Resources\StaffUserResource\Pages;
use App\Models\StaffUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StaffUserResource extends Resource
{
    protected static ?string $model = StaffUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function getModelLabel(): string
    {
        return __('filament.Staff User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.Staff Users');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.Pengaturan');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isManager() ?? false;
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->isManager() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('filament.Name'))
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('email')
                    ->label(__('filament.Email'))
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->label(__('filament.Password'))
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state): bool => filled($state))
                    ->maxLength(255),
                Forms\Components\Select::make('role')
                    ->label(__('filament.Role'))
                    ->options(self::staffRoleOptions())
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament.Email'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->label(__('filament.Role'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => StaffRole::from(self::enumValue($state))->label())
                    ->color(fn ($state): string => StaffRole::from(self::enumValue($state)) === StaffRole::Manager ? 'primary' : 'gray'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('filament.Edit')),
                Tables\Actions\DeleteAction::make()->label(__('filament.Delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('filament.Delete Selected')),
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label(__('filament.New')),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaffUsers::route('/'),
            'create' => Pages\CreateStaffUser::route('/create'),
            'edit' => Pages\EditStaffUser::route('/{record}/edit'),
        ];
    }

    private static function staffRoleOptions(): array
    {
        $options = [];

        foreach (StaffRole::cases() as $role) {
            $options[$role->value] = $role->label();
        }

        return $options;
    }

    private static function enumValue(mixed $state): string
    {
        return $state instanceof \BackedEnum ? $state->value : (string) $state;
    }
}