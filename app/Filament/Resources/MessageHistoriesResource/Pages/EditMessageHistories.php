<?php

namespace App\Filament\Resources\MessageHistoriesResource\Pages;

use App\Filament\Resources\MessageHistoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMessageHistories extends EditRecord
{
    protected static string $resource = MessageHistoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
