<?php

namespace App\Providers;

use App\Repositories\Interfaces\GameRepositoryInterface;
use App\Repositories\Interfaces\NewsRepositoryInterface;
use App\Repositories\Interfaces\SeasonRepositoryInterface;
use App\Repositories\NewsRepository;
use App\Repositories\SeasonRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\GameRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            GameRepositoryInterface::class,
            GameRepository::class
        );

        $this->app->bind(
            SeasonRepositoryInterface::class,
            SeasonRepository::class
        );

        $this->app->bind(
            NewsRepositoryInterface::class,
            NewsRepository::class
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
