<?php

namespace App\Providers;

use App\Jobs\PayoutOrderJob;
use App\Services\ApiService;
use App\Services\MerchantService;
use App\Interfaces\MerchantServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bindMethod([PayoutOrderJob::class, 'handle'], function ($job, $app) {
            return $job->handle($app->make(ApiService::class));
        });
    }
}
