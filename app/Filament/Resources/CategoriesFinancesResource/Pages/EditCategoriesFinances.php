<?php

namespace App\Filament\Resources\CategoriesFinancesResource\Pages;

use App\Filament\Resources\CategoriesFinancesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoriesFinances extends EditRecord
{
    protected static string $resource = CategoriesFinancesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
