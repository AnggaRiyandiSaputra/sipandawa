<?php

namespace App\Filament\Resources\TransactionsResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Resources\TransactionsResource;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null =>Tab::make('All'),
            'Operasional'=> Tab::make()->query(fn($query)=> $query->where('categorie_finance_id',1)),
            'Kas' => Tab::make()->query(fn($query)=> $query->where('categorie_finance_id',2)),
            'Pajak' => Tab::make()->query(fn($query)=> $query->where('categorie_finance_id',3)),
        ];
    }
}
