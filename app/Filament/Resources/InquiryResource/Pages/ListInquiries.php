<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Filament\Resources\InquiryResource;
use Filament\Resources\Pages\ListRecords;

class ListInquiries extends ListRecords
{
    protected static string $resource = InquiryResource::class;

    public function getTitle(): string
    {
        return 'Consultas';
    }

    public function getHeading(): string
    {
        return 'Gestión de consultas de clientes';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
