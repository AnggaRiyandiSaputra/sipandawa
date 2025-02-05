<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriesFinancesResource\Pages;
use App\Filament\Resources\CategoriesFinancesResource\RelationManagers;
use App\Models\CategoriesFinances;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoriesFinancesResource extends Resource
{
    protected static ?string $model = CategoriesFinances::class;

    protected static ?string $navigationIcon = 'heroicon-s-list-bullet';

    protected static ?string $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 21;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\Select::make('type')
                            ->options([
                                'Pemasukan' => 'Pemasukan',
                                'Pengeluaran' => 'Pengeluaran'
                            ])
                            ->native(false)
                            ->required(),
                    ])
                    ->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pengeluaran' => 'danger',
                        'Pemasukan' => 'success',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListCategoriesFinances::route('/'),
            'create' => Pages\CreateCategoriesFinances::route('/create'),
            'edit' => Pages\EditCategoriesFinances::route('/{record}/edit'),
        ];
    }
}
