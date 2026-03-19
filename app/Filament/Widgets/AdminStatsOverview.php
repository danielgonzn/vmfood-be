<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProducts = Product::query()->count();
        $activeProducts = Product::query()->where('available', true)->count();
        $totalUsers = User::query()->count();
        $newInquiries = Inquiry::query()->where('status', 'new')->count();

        return [
            Stat::make('Productos totales', (string) $totalProducts)
                ->description('Registrados en catálogo')
                ->icon('heroicon-o-cube')
                ->color('primary'),
            Stat::make('Productos activos', (string) $activeProducts)
                ->description('Disponibles para cotizar')
                ->icon('heroicon-o-check-badge')
                ->color('success'),
            Stat::make('Usuarios del sistema', (string) $totalUsers)
                ->description('Cuentas administrativas')
                ->icon('heroicon-o-users')
                ->color('info'),
            Stat::make('Consultas nuevas', (string) $newInquiries)
                ->description('Pendientes por atender')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('warning'),
        ];
    }
}
