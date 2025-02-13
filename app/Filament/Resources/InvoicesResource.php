<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Invoices;
use Filament\Forms\Form;
use Filament\Tables\Table;
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
                Forms\Components\Section::make()
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
                                Forms\Components\FileUpload::make('image')
                                    ->label('bukti tf')
                                    ->columnSpanFull()
                                    ->image()
                                    ->downloadable()
                                    ->openable()
                                    ->optimize('webp')
                                    ->resize(50)
                                    ->directory('bukti_tf')
                                    ->acceptedFileTypes(['image/jpg', 'image/png', 'image/jpeg']),
                            ]),
                    ])->columnSpan([
                        'sm' => 1,
                        'lg' => 2
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Toggle::make('is_paid')
                                    ->label('Is paid ?')
                                    ->default(0)
                                    ->required()
                                    ->reactive(),
                                Forms\Components\DatePicker::make('paid_date')
                                    ->disabled(function (callable $get) {
                                        return !$get('is_paid'); // Nonaktifkan jika is_paid = 0
                                    }),
                            ]),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('sub_total')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->reactive(), // Memantau perubahan sub_total

                                Forms\Components\Toggle::make('is_pajak')
                                    ->label('Include Pajak?')
                                    ->default(0)
                                    ->required()
                                    ->reactive() // Memantau perubahan is_pajak
                                    ->afterStateUpdated(function (callable $set, $get) {
                                        // Hitung ulang grand_total jika is_pajak berubah
                                        $subTotal = (float) ($get('sub_total') ?: 0);
                                        $diskon = (float) ($get('diskon') ?: 0);
                                        $isPajak = (bool) ($get('is_pajak') ?: 0);

                                        $pajak = $isPajak ? ($subTotal * 0.11) : 0;
                                        $grandTotal = $subTotal + $pajak - $diskon;

                                        $set('grand_total', $grandTotal);
                                    }),

                                Forms\Components\TextInput::make('diskon')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->reactive() // Memantau perubahan diskon
                                    ->afterStateUpdated(function (callable $set, $get) {
                                        // Hitung ulang grand_total jika diskon berubah
                                        $subTotal = (float) ($get('sub_total') ?: 0);
                                        $diskon = (float) ($get('diskon') ?: 0);
                                        $isPajak = (bool) ($get('is_pajak') ?: 0);

                                        $pajak = $isPajak ? ($subTotal * 0.11) : 0;
                                        $grandTotal = $subTotal + $pajak - $diskon;

                                        $set('grand_total', $grandTotal);
                                    }),

                                Forms\Components\TextInput::make('grand_total')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->reactive(),

                            ]),
                    ])->columnSpan([
                        1
                    ]),
                Forms\Components\Section::make()
                    ->schema([
                        Repeater::make('detailInvoice')
                            ->relationship()
                            ->label('Item')
                            ->schema([
                                Forms\Components\Textarea::make('item')
                                    ->required(),

                                // Forms\Components\TextInput::make('qty')
                                //     ->numeric()
                                //     ->reactive()
                                //     ->required(),
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->prefix('Rp.')
                                    ->reactive()
                                    ->required(),
                                // Forms\Components\TextInput::make('total')
                                //     ->numeric()
                                //     ->prefix('Rp.')
                                //     ->disabled()
                                //     ->required()
                                //     ->reactive()
                                //     ->dehydrated(false) // Opsional: Jangan kirim data ini ke backend
                                //     ->afterStateHydrated(function (callable $set, $get) {
                                //         // Hitung total saat form dimuat
                                //         $set('total', ($get('qty') ?: 0) * ($get('price') ?: 0));
                                //     })
                                //     ->afterStateUpdated(function (callable $set, $get) {
                                //         // Hitung total saat qty atau price diperbarui
                                //         $set('total', ($get('qty') ?: 0) * ($get('price') ?: 0));
                                //     }),
                            ])
                            ->columns(2)
                            ->reactive() // Membuat Repeater memantau perubahan pada elemen
                            ->afterStateUpdated(function (callable $set, $get) {
                                // Ambil semua detail invoice
                                $detailInvoice = $get('detailInvoice') ?? [];

                                // Hitung total dari semua harga
                                $subTotal = collect($detailInvoice)->sum('price');

                                // Set nilai ke field sub_total
                                $set('sub_total', $subTotal);
                            })
                            ->defaultItems(1),
                    ]),
            ])->columns(3);
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
