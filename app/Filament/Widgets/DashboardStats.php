<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DashboardStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Monthly Revenue
        $monthlyRevenue = InvoiceItem::query()
            ->whereHas('invoice', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'paid')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            })
            ->sum('amount');

        // Unpaid Invoices
        $unpaidInvoices = $user->invoices()->whereIn('status', ['draft', 'sent', 'overdue'])->count();

        // Clients
        $clientsCount = $user->clients()->count();


        return [
            Stat::make('Revenue This Month', '₹' . number_format($monthlyRevenue, 2))
                ->description('Paid invoices')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Unpaid Invoices', $unpaidInvoices)
                ->description('Require attention')
                ->icon('heroicon-o-exclamation-circle')
                ->color('danger'),

            Stat::make('Total Clients', $clientsCount)
                ->description('Active clients')
                ->icon('heroicon-o-user-group')
                ->color('info')
        ];
    }
}
