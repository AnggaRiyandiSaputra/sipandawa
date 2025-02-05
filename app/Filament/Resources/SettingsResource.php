<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Settings;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use App\Filament\Resources\SettingsResource\Pages;
use Closure;

class SettingsResource extends Resource
{
    protected static ?string $model = Settings::class;

    protected static ?string $navigationIcon = 'heroicon-m-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 61;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->heading('*kas + pajak + komisi = 100%')
                    ->schema([
                        Forms\Components\TextInput::make('kas')
                            ->required()
                            ->suffix('%')
                            ->rules([
                                fn(): Closure => function (string $attribute, $value, Closure $fail) {
                                    if ($value == 0) {
                                        $fail(':attribute tidak boleh 0.');
                                    }
                                },
                            ])
                            ->numeric(),
                        Forms\Components\TextInput::make('pajak')
                            ->required()
                            ->suffix('%')
                            ->rules([
                                fn(): Closure => function (string $attribute, $value, Closure $fail) {
                                    if ($value == 0) {
                                        $fail(':attribute tidak boleh 0.');
                                    }
                                },
                            ])
                            ->numeric(),
                        Forms\Components\TextInput::make('komisi')
                            ->required()
                            ->suffix('%')
                            ->rules([
                                fn(): Closure => function (string $attribute, $value, Closure $fail) {
                                    if ($value == 0) {
                                        $fail(':attribute tidak boleh 0.');
                                    }
                                },
                            ])
                            ->numeric(),
                    ])
                    ->columns(2),
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
            'index' => Pages\EditSettings::route('/'),
            'edit' => Pages\EditSettings::route('/{record}/edit'),
        ];
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl('edit', ['record' => 1]);
    }
}
