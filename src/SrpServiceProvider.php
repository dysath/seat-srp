<?php

namespace Denngarr\Seat\SeatSrp;

use Denngarr\Seat\SeatSrp\Commands\InsuranceUpdate;
use Illuminate\Support\ServiceProvider;

class SrpServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->addCommands();
        $this->add_routes();
        // $this->add_middleware($router);
        $this->add_views();
        $this->add_publications();
        $this->add_translations();
    }

    /**
     * Include the routes.
     */
    public function add_routes()
    {
        if (! $this->app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }
    }

    public function add_translations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'srp');
    }

    /**
     * Set the path and namespace for the views.
     */
    public function add_views()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'srp');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/srp.config.php', 'srp.config');

        $this->mergeConfigFrom(
            __DIR__ . '/Config/srp.sidebar.php', 'package.sidebar');

        $this->mergeConfigFrom(
            __DIR__ . '/Config/srp.permissions.php', 'web.permissions');
    }

    public function add_publications()
    {
        $this->publishes([
            __DIR__ . '/resources/assets'     => public_path('web'),
            __DIR__ . '/database/migrations/' => database_path('migrations')
        ]);
    }

    private function addCommands()
    {
        $this->commands([
            InsuranceUpdate::class,
        ]);
    }

}
