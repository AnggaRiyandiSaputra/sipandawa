<?php

namespace App\Filament\Resources;


use Filament\Forms;
use Filament\Tables;
use App\Models\Projects;
use Filament\Forms\Form;
use App\Models\Employees;
use Filament\Tables\Table;
use App\Models\Transactions;
use App\Models\Detail_Project;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectsResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProjectsResource\RelationManagers;

class ProjectsResource extends Resource
{
    protected static ?string $model = Projects::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Project';

    protected static ?int $navigationSort = 33;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('no_projek')
                                    ->disabled()
                                    ->required()
                                    ->dehydrated()
                                    ->default('PRJ-' . random_int(100000, 999999))
                                    ->maxLength(32)
                                    ->unique(Projects::class, 'no_projek', ignoreRecord: true),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('anggota_id')
                                    ->label("penanggung jawab")
                                    ->relationship('penanggungjawab', 'name')
                                    ->required(),
                                Forms\Components\Select::make('client_id')
                                    ->label('client')
                                    ->relationship('client', 'name')
                                    ->required(),
                                Forms\Components\Textarea::make('description')
                                    ->columnSpanFull(),
                                Forms\Components\DatePicker::make('start_date')
                                    ->required(),
                                Forms\Components\DatePicker::make('end_date')
                                    ->required(),
                                Forms\Components\Select::make('is_done')
                                    ->label('status')
                                    ->options([
                                        'Not Started' =>  'Not Started',
                                        'In Progress' => 'In Progress',
                                        'Done' => 'Done'
                                    ])
                                    ->native(false)
                                    ->default('Not Started')
                                    ->required(),
                                Forms\Components\Select::make('invoice_status')
                                    ->options([
                                        'Unpaid' => 'Unpaid',
                                        'Paid' => 'Paid'
                                    ])
                                    ->default('Unpaid')
                                    ->native(false)
                                    ->disabled(fn($get) => $get('invoice_status') === 'Paid')
                                    ->dehydrated()
                                    ->required(),
                            ]),
                        Forms\Components\Section::make()
                            ->schema([
                                Repeater::make('detailProject')
                                    ->label('Anggota Projek')
                                    ->schema([
                                        Forms\Components\Select::make('anggota_id_anggota')
                                            ->label('Anggota')
                                            ->options(Employees::query()->pluck('name', 'id'))
                                            ->required(),
                                    ])
                                    ->relationship()
                                    ->columnSpanFull()
                                    ->defaultItems(1),
                            ]),
                    ])->columnSpan([
                        'sm' => 1,
                        'lg' => 2
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp.')
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        $set('kas', $state * 0.09); // 9% dari price
                                        $set('pajak', $state * 0.11); // 12% dari price
                                        $set('komisi', $state * 0.80); // 78% dari price
                                    }),
                                Forms\Components\TextInput::make('pajak')
                                    ->label('Pajak(11% dari price)')
                                    ->required()
                                    // ->disabled()
                                    ->dehydrated()
                                    ->prefix('Rp.'),
                                Forms\Components\TextInput::make('kas')
                                    ->label('Kas(9% dari price)')
                                    ->required()
                                    // ->disabled()
                                    ->dehydrated()
                                    ->prefix('Rp.'),
                                Forms\Components\TextInput::make('komisi')
                                    ->label('Komisi(80% dari price)')
                                    ->required()
                                    // ->disabled()
                                    ->dehydrated()
                                    ->prefix('Rp.'),
                            ]),
                    ])->columnSpan([
                        1
                    ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_projek')
                    ->searchable(),
                Tables\Columns\TextColumn::make('penanggungjawab.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('client.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('Rp.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pajak')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('komisi')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('is_done')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Not Started' => 'danger',
                        'In Progress' => 'warning',
                        'Done' => 'success',
                    }),
                Tables\Columns\TextColumn::make('invoice_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Unpaid' => 'danger',
                        'Paid' => 'success',
                    }),
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
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProjects::route('/create'),
            'edit' => Pages\EditProjects::route('/{record}/edit'),
        ];
    }
}
