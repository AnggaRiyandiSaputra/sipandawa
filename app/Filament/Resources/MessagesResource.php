<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Messages;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MessagesResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MessagesResource\RelationManagers;

class MessagesResource extends Resource
{
    protected static ?string $model = Messages::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Message';

    protected static ?int $navigationSort = 41;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Penanggung Jawab')
                            ->relationship('Employee', 'name')
                            ->required()
                            ->disabled()
                            ->default(fn($record) => $record?->employee_id),
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->required()
                            ->disabled()
                            ->default(fn($record) => $record?->client_id),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->default(fn($record) => $record?->name),
                        Forms\Components\DateTimePicker::make('schedule'),
                        Forms\Components\Textarea::make('message')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

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
                Tables\Columns\TextColumn::make('no_invoice')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('schedule')
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Send Message')
                    ->icon('heroicon-s-arrow-up-right')
                    ->action(function (Messages $record) {
                        return redirect()->route('send-message', $record); // Redirect setelah aksi dijalankan
                    })
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengiriman Pesan')
                    ->modalSubheading(function (Messages $record) {
                        // Menambahkan nomor telepon Employee dan Client ke dalam modalSubheading
                        $employeePhone =  $record->employee->phone;
                        $clientPhone =  $record->client->phone;

                        return "Apakah Anda yakin ingin mengirim pesan ini?\n\nKe nomor PJ: {$employeePhone}\nClient Phone: {$clientPhone}";
                    })
                    ->modalButton('Kirim'),

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
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessages::route('/create'),
            'edit' => Pages\EditMessages::route('/{record}/edit'),
        ];
    }
}
