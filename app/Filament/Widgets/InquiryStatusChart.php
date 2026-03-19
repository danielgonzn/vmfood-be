<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use Filament\Widgets\ChartWidget;

class InquiryStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Consultas por estado';

    protected static ?string $maxHeight = '500px';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $new = Inquiry::query()->where('status', 'new')->count();
        $contacted = Inquiry::query()->where('status', 'contacted')->count();
        $closed = Inquiry::query()->where('status', 'closed')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Consultas',
                    'data' => [$new, $contacted, $closed],
                    'backgroundColor' => ['#f59e0b', '#0ea5e9', '#22c55e'],
                ],
            ],
            'labels' => ['Nuevas', 'Contactadas', 'Cerradas'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
