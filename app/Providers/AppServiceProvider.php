<?php

namespace App\Providers;

use App\Services\DashboardShellDataBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        View::composer('layouts.dashboard-shell', function (ViewContract $view): void {
            /** @var DashboardShellDataBuilder $builder */
            $builder = app(DashboardShellDataBuilder::class);
            $view->with($builder->build($view->getData()));
        });
    }
}
