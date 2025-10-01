<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Invoices;
use App\Models\Settings;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\InvoicesResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Factories\Relationship;
use App\Filament\Resources\InvoicesResource\RelationManagers;

class InvoicesResource extends Resource
{
    protected static ?string $model = Invoices::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 23;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                     Forms\Components\Wizard\Step::make('Detail Invoice')
                    ->schema([
                        Forms\Components\Grid::make()                            
                            ->schema([
                                Forms\Components\TextInput::make('no_invoice')
                                    ->disabled()
                                    ->columnSpanFull()
                                    ->required()
                                    ->dehydrated()
                                    ->default('INV-' . random_int(100000, 999999))
                                    ->maxLength(32)
                                    ->unique(Invoices::class, 'no_invoice', ignoreRecord: true),
                                Forms\Components\Select::make('employee_id')
                                    ->label('Penanggung Jawab')
                                    ->relationship('Employee', 'name')
                                    ->required(),
                                Forms\Components\Select::make('client_id')
                                    ->Relationship('Client', 'name')
                                    ->searchable()         
                                    ->preload()       
                                    ->loadingMessage('Loading client...')                    
                                    ->noSearchResultsMessage('No client found.')
                                    ->searchingMessage('Searching client...')
                                    ->placeholder('Select a client')
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('issued_date')
                                    ->required(),
                                Forms\Components\DatePicker::make('due_date')
                                    ->required(),
                                  Forms\Components\Toggle::make('is_paid')
                                    ->label('Is paid ?')
                                    ->default(0)
                                    ->required()
                                    ->reactive(),
                                Forms\Components\DatePicker::make('paid_date')
                                    ->disabled(function (callable $get) {
                                        return !$get('is_paid'); // Nonaktifkan jika is_paid = 0
                                    }),
                                Forms\Components\FileUpload::make('image')
                                    ->label('bukti tf')
                                    ->columnSpanFull()
                                    ->image()
                                    ->downloadable()
                                    ->openable()                                    
                                    ->directory('bukti_tf')
                                    ->acceptedFileTypes(['image/jpg', 'image/png', 'image/jpeg']),
                            ]),
                        ]),
                 Forms\Components\Wizard\Step::make('Item')
                    ->schema([
                        Repeater::make('detailInvoice')
                            ->relationship()
                            ->label('Item')
                            ->schema([
                                Forms\Components\Textarea::make('item')
                                    ->required(),
                                Forms\Components\TextInput::make('price')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->numeric()
                                    ->prefix('Rp.')                                    
                                    ->required()
                                    ->beforeStateDehydrated(fn ($state) => str_replace(',', '', $state))
                            ])
                            ->columns(2)                            
                            ->afterStateUpdated(function (callable $set, $get) {
                                // Ambil semua detail invoice
                                $detailInvoice = $get('detailInvoice') ?? [];
                                
                                // Hitung total dari semua harga
                                $subTotal = collect($detailInvoice)->sum(function ($item) {
                                    return (int) str_replace(',', '', $item['price'] ?? 0);
                                });

                                // Set nilai ke field sub_total
                                $set('sub_total', number_format($subTotal, 0, '', ','));
                            })
                            ->defaultItems(1),
                    ]),
                Forms\Components\Wizard\Step::make('Total')
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('sub_total')
                                    ->required()
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->numeric()
                                    ->default(0)
                                    ->beforeStateDehydrated(fn ($state) => str_replace(',', '', $state)),                            

                                Forms\Components\Toggle::make('is_pajak')
                                    ->label('Include Pajak?')
                                    ->default(0)
                                    ->required(),                                    
                                    
                                Forms\Components\TextInput::make('diskon')
                                    ->required()
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->numeric()
                                    ->default(0)
                                    ->beforeStateDehydrated(fn ($state) => str_replace(',', '', $state)),

                                Forms\Components\TextInput::make('pajak')
                                ->label(
                                    'Pajak ('.(Settings::getPajak()).'%)'
                                )
                                ->disabled(),

                                Forms\Components\Hidden::make('pajak_rate')
                                    ->default(Settings::getPajak()),
                                   
                                Forms\Components\TextInput::make('grand_total')
                                    ->required()
                                   ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->numeric()
                                    ->default(0)
                                    ->beforeStateDehydrated(fn ($state) => str_replace(',', '', $state))                                
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('Recalculate')
                                            ->label('Recalculate Total')
                                            ->icon('heroicon-o-arrows-right-left')
                                            ->color('success')
                                            ->action(function (callable $get, callable $set) {
                                                $subTotal = str_replace(',', '', $get('sub_total')) ?? 0;
                                                $diskon = str_replace(',', '', $get('diskon')) ?? 0;
                                                $isPajak = $get('is_pajak') ?? 0;
                                                $pajakRate = Settings::getPajak() / 100;

                                                // Hitung pajak jika is_pajak diaktifkan
                                                $pajak = $isPajak ? ($subTotal - $diskon) * $pajakRate : 0;

                                                // Hitung grand total
                                                $grandTotal = ($subTotal - $diskon) + $pajak;

                                                // Set nilai pajak dan grand_total
                                                $set('pajak', number_format($pajak, 0, '', ','));
                                                $set('grand_total', number_format($grandTotal, 0, '', ','));
                                            })
                                    ),
                            ]),
                        ]),               
                ])->columnSpanFull(),               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_invoice')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean(),
                Tables\Columns\TextColumn::make('grand_total')
                    ->money('Rp.')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Preview')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Invoices $record): string => route('preview-invoice', $record))
                    ->color('grey')
                    ->openUrlInNewTab(),
                Action::make('Make Message')
                    ->icon('heroicon-s-document-text')
                    ->url(fn(Invoices $record): string => route('make-message', $record))
                    ->color('success'),
                Action::make('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn(Invoices $record): string => route('download-invoice', $record))
                    ->color('success'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoices::route('/create'),
            'edit' => Pages\EditInvoices::route('/{record}/edit'),
        ];
    }
}
