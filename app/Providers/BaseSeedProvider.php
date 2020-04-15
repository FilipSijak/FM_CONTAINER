<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Services\GameService\GameData\GameInitialDataSeed;
use Services\GameService\Interfaces\GameInitialDataSeedInterface;

class BaseSeedProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            GameInitialDataSeedInterface::class,
            GameInitialDataSeed::class
        );
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
