<?php

namespace App\Providers;

use App\GameEngine\GameContainer;
use App\GameEngine\Interfaces\GameContainerInterface;
use Illuminate\Support\ServiceProvider;

class GameEngineProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            GameContainerInterface::class,
            GameContainer::class
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
