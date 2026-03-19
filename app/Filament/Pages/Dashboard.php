<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getTitle(): string
    {
        return 'Dashboard';
    }

    public function getHeading(): string
    {
        return 'Panel de control';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_inquiries_csv')
                ->label('Exportar consultas CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('admin.reports.inquiries.csv')),
            Action::make('export_inquiries_xlsx')
                ->label('Exportar consultas Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->url(route('admin.reports.inquiries.xlsx')),
        ];
    }
}
