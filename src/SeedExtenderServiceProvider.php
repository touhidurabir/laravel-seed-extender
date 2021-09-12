<?php

namespace Touhidurabir\SeedExtender;

use Touhidurabir\SeedExtender\Seeder;
use Illuminate\Support\ServiceProvider;
use Touhidurabir\SeedExtender\Console\ExtendSeeder;


class SeedExtenderServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {

        if ( $this->app->runningInConsole() ) {
			$this->commands([
				ExtendSeeder::class
			]);
		}
    }

    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {

        $this->app->bind('seed-extender', function () {
            
            return new Seeder;
        });
    }
}