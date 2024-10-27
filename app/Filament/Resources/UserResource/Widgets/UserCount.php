<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserCount extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $users = User::where('type', 0)->count();
        $sellers = User::where('type', 1)->count();
        $admins = User::where('type', 2)->count();
        return [
            Stat::make('Users', $users)
            ->description('Total number of users')
            ->icon('heroicon-o-user-group')
            ->color('primary')
            ->url('/admin/users'),
            Stat::make('Sellers', $sellers)
            ->description('Total number of sellers')
            ->icon('heroicon-o-user-group')
            ->color('primary')
            ->url('/admin/sellers'),
            Stat::make('Admins', $admins)
            ->description('Total number of admins')
            ->icon('heroicon-o-user-group')
            ->color('primary')
            ->url('/admin/users'),
            Stat::make('Total', $totalUsers)
            ->description('Total number of users')
            ->icon('heroicon-o-user-group')
            ->color('primary')
            ->url('/admin/users'),
        ];
    }
}
