<?php

namespace App\Providers;

use App\Repositories\AbsensiRepository;
use App\Repositories\Contracts\AbsensiRepositoryInterface;
use App\Repositories\Contracts\KelasRepositoryInterface;
use App\Repositories\Contracts\SiswaRepositoryInterface;
use App\Repositories\KelasRepository;
use App\Repositories\SiswaRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Class RepositoryServiceProvider
 *
 * Service provider for binding repository interfaces to their implementations.
 * This allows dependency injection throughout the application.
 *
 * @package App\Providers
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * Bind repository interfaces to their concrete implementations.
     * This allows the application to use dependency injection for repositories.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(SiswaRepositoryInterface::class, SiswaRepository::class);
        $this->app->bind(AbsensiRepositoryInterface::class, AbsensiRepository::class);
        $this->app->bind(KelasRepositoryInterface::class, KelasRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
