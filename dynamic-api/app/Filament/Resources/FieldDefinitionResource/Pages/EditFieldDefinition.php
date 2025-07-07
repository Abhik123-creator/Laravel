<?php

namespace App\Filament\Resources\FieldDefinitionResource\Pages;

use App\Filament\Resources\FieldDefinitionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFieldDefinition extends EditRecord
{
    protected static string $resource = FieldDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
