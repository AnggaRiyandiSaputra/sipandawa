<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageHistoriesResource\Pages;
use App\Filament\Resources\MessageHistoriesResource\RelationManagers;
use App\Models\MessageHistories;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MessageHistoriesResource extends Resource
{
    protected static ?string $model = MessageHistories::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Message';

    protected static ?int $navigationSort = 42;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_pj')
                    ->label('Nama PJ')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name_client')
                    ->label('Nama Client')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('send_to')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('created_at'),
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('status')
                    ->required(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name_client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('send_to')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListMessageHistories::route('/'),
            'create' => Pages\CreateMessageHistories::route('/create'),
            'edit' => Pages\EditMessageHistories::route('/{record}/edit'),
        ];
    }
}
