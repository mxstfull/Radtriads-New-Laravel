<?php

namespace App\Providers;
use App\ShaHasher;
use Illuminate\Hashing\HashServiceProvider;

use Illuminate\Support\ServiceProvider;

class ShaHashServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('hash', function() { return new ShaHasher; });
        
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function provides() {
        return array('hash');
      }
}
