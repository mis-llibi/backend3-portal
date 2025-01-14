<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use TCPDF;

class PDFServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('pdf', function ($app) {
            return new TCPDF();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
