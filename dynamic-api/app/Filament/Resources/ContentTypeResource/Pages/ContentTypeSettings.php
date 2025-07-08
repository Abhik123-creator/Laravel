<?php

namespace App\Filament\Resources\ContentTypeResource\Pages;

use App\Filament\Resources\ContentTypeResource;
use Filament\Resources\Pages\Page;

class ContentTypeSettings extends Page
{
    protected static string $resource = ContentTypeResource::class;

    protected static string $view = 'filament.resources.content-type-resource.pages.content-type-settings';
}
