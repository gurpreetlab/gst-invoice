<?php

namespace App\Filament\Widgets;

use App\Models\InvoiceItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue';

    public ?string $filter = 'month';

    protected function getFilters(): array
    {
        return [
            'week' => 'Last 7 Days',
            'month' => 'This Month',
            'year' => 'This Year',
        ];
    }

    protected function getData(): array
    {
        $userId = Auth::id();

        return match ($this->filter) {
            'week' => $this->weeklyRevenue($userId),
            'year' => $this->yearlyRevenue($userId),
            default => $this->monthlyRevenue($userId),
        };
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function weeklyRevenue(int $userId): array
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);

            $labels[] = $date->format('D');

            $data[] = InvoiceItem::query()
                ->whereHas('invoice', function ($query) use ($userId, $date) {
                    $query->where('user_id', $userId)
                        ->where('status', 'paid')
                        ->whereDate('created_at', $date);
                })->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function monthlyRevenue(int $userId): array
    {
        $labels = [];
        $data = [];

        foreach (range(1, now()->daysInMonth) as $day) {
            $labels[] = $day;

            $data[] = InvoiceItem::query()
                ->whereHas('invoice', function ($query) use ($userId, $day) {
                    $query->where('user_id', $userId)
                        ->where('status', 'paid')
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->whereDay('created_at', $day);
                })->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data
                ],
            ],
            'labels' => $labels
        ];
    }

    private function yearlyRevenue(int $userId): array
    {
        $labels = [];
        $data = [];

        foreach (range(1, 12) as $month) {
            $labels[] = now()->startOfYear()->addMonths($month - 1)->format('M');

            $data[] = InvoiceItem::query()
                ->whereHas('invoice', function ($query) use ($userId, $month) {
                    $query->where('user_id', $userId)
                        ->where('status', 'paid')
                        ->whereYear('created_at', now()->year)
                        ->whereMonth('created_at', $month);
                })->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data
                ],
            ],
            'labels' => $labels
        ];
    }
}
