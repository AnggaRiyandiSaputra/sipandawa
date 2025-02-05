<?php

namespace App\Filament\Resources\MessageHistoriesResource\Pages;

use App\Filament\Resources\MessageHistoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMessageHistories extends ListRecords
{
    protected static string $resource = MessageHistoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
