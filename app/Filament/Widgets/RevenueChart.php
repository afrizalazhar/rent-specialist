<?php

namespace App\Filament\Widgets;

use App\Models\PaymentRecord;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected static ?int $sort = 2;

    /**
     * Full width on mobile, half on md+ so it sits beside RecentBookings.
     */
    protected int | string | array $columnSpan = ['default' => 'full', 'md' => 1, 'xl' => 1];

    protected static ?string $heading = null;

    public function getHeading(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return __('filament.Revenue Last 30 Days');
    }

    public function getDescription(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return __('filament.Daily payment totals in IDR');
    }

    protected function getData(): array
    {
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();

        $records = PaymentRecord::query()
            ->whereBetween('received_at', [$start, $end])
            ->selectRaw('DATE(received_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->all();

        $labels = [];
        $data = [];

        for ($day = 0; $day < 30; $day++) {
            $date = now()->subDays(29 - $day)->format('Y-m-d');
            $labels[] = now()->subDays(29 - $day)->format('d M');
            $data[] = (int) ($records[$date] ?? 0);
        }

        return [
            'datasets' => [
                [
                'label' => __('filament.Revenue') . ' (IDR)',
                'data' => $data,
                'backgroundColor' => 'rgba(0, 0, 0, 0.08)',
                'borderColor' => '#000000',
                'borderWidth' => 2,
                'borderRadius' => 6,
                'fill' => true,
                'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'align' => 'end',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'boxWidth' => 8,
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
        ];
    }
}
