<?php

namespace App\Providers;

use App\Charts\OrdersChart;
use App\Charts\OrdersChartTotals;
use App\Charts\RevenueChart;
use App\Charts\RevenueChartTotals;
use App\Charts\SalesChart;
use App\Charts\SalesChartTotals;
use ConsoleTVs\Charts\Registrar as Charts;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Charts $charts)
    {
        $charts->register([
            SalesChart::class,
            OrdersChart::class,
            RevenueChart::class,
            RevenueChartTotals::class,
            OrdersChartTotals::class,
            SalesChartTotals::class
        ]);
    }
}
