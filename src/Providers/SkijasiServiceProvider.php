<?php

namespace NadzorServera\Skijasi\Providers;

use Arcanedev\LogViewer\LogViewerServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use L5Swagger\L5SwaggerServiceProvider;
use Larapack\DoctrineSupport\DoctrineSupportServiceProvider;
use NadzorServera\Skijasi\Skijasi;
use NadzorServera\Skijasi\Commands\AdminCommand;
use NadzorServera\Skijasi\Commands\BackupCommand;
use NadzorServera\Skijasi\Commands\SkijasiFirebaseCommand;
use NadzorServera\Skijasi\Commands\SkijasiSetup;
use NadzorServera\Skijasi\Commands\SkijasiTestSetup;
use NadzorServera\Skijasi\Commands\GenerateSeederCommand;
use NadzorServera\Skijasi\Facades\Skijasi as FacadesSkijasi;
use NadzorServera\Skijasi\Middleware\CheckForMaintenanceMode;
use NadzorServera\Skijasi\Middleware\GenerateForSwagger;

class SkijasiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('Skijasi', FacadesSkijasi::class);

        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', CheckForMaintenanceMode::class);
        $router->pushMiddlewareToGroup('web', GenerateForSwagger::class);

        $this->app->singleton('skijasi', function () {
            return new Skijasi();
        });

        $this->loadMigrationsFrom(__DIR__.'/../Migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'skijasi');
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'skijasi');

        $this->publishes([
            __DIR__.'/../Config/skijasi.php' => config_path('skijasi.php'),
            __DIR__.'/../Config/log-viewer.php' => config_path('log-viewer.php'),
            __DIR__.'/../Config/backup.php' => config_path('backup.php'),
            __DIR__.'/../Seeder/Configurations' => database_path('seeders/Skijasi'),
            __DIR__.'/../Seeder/CRUD' => database_path('seeders/Skijasi/CRUD'),
            __DIR__.'/../Images/skijasi-images/' => storage_path('app/public/photos/shares'),
            __DIR__.'/../Seeder/ManualGenerate' => database_path('seeders/Skijasi/ManualGenerate'),
            __DIR__.'/../resources/customization/' => resource_path('js/skijasi'),
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/skijasi'),
            __DIR__.'/../Swagger/swagger_models' => app_path('Http/Swagger/swagger_models'),
            __DIR__.'/../Config/lfm.php' => config_path('lfm.php'),
            __DIR__.'/../Config/firebase.php' => config_path('firebase.php'),
            __DIR__.'/../Config/l5-swagger.php' => config_path('l5-swagger.php'),
            __DIR__.'/../resources/views/vendor' => resource_path('views/vendor'),
            __DIR__.'/../Config/skijasi-hidden-tables.php' => config_path('skijasi-hidden-tables.php'),
            __DIR__.'/../Config/skijasi-watch-tables.php' => config_path('skijasi-watch-tables.php'),
            __DIR__.'/../Config/analytics.php' => config_path('analytics.php'),
            __DIR__.'/../Config/octane.php' => config_path('octane.php'),
        ], 'Skijasi');

        $this->publishes([
            __DIR__.'/../Config/skijasi.php' => config_path('skijasi.php'),
            __DIR__.'/../Config/log-viewer.php' => config_path('log-viewer.php'),
            __DIR__.'/../Config/backup.php' => config_path('backup.php'),
            __DIR__.'/../Config/lfm.php' => config_path('lfm.php'),
            __DIR__.'/../Config/firebase.php' => config_path('firebase.php'),
            __DIR__.'/../Config/l5-swagger.php' => config_path('l5-swagger.php'),
            __DIR__.'/../Config/skijasi-hidden-tables.php' => config_path('skijasi-hidden-tables.php'),
            __DIR__.'/../Config/skijasi-watch-tables.php' => config_path('skijasi-watch-tables.php'),
            __DIR__.'/../Config/analytics.php' => config_path('analytics.php'),
        ], 'SkijasiConfig');

        $this->publishes([
            __DIR__.'/../Seeder/Configurations' => database_path('seeders/Skijasi'),
            __DIR__.'/../Seeder/CRUD' => database_path('seeders/Skijasi/CRUD'),
            __DIR__.'/../Seeder/ManualGenerate' => database_path('seeders/Skijasi/ManualGenerate'),
        ], 'SkijasiSeeder');

        $this->publishes([
            __DIR__.'/../resources/customization/' => resource_path('js/skijasi'),
            __DIR__.'/../Images/skijasi-images/' => storage_path('app/public/photos/shares'),
            __DIR__.'/../resources/views/vendor' => resource_path('views/vendor'),
            // __DIR__.'/../resources/lang' => resource_path('lang/vendor/skijasi'),
        ], 'SkijasiResource');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(OrchestratorEventServiceProvider::class);
        $this->app->register(DoctrineSupportServiceProvider::class);
        $this->app->register(DropboxServiceProvider::class);
        $this->app->register(GoogleDriveServiceProvider::class);
        $this->app->register(LogViewerServiceProvider::class);
        $this->app->register(L5SwaggerServiceProvider::class);
        $this->registerConsoleCommands();
    }

    /**
     * Register the commands accessible from the Console.
     */
    private function registerConsoleCommands()
    {
        $this->commands(SkijasiSetup::class);
        $this->commands(AdminCommand::class);
        $this->commands(BackupCommand::class);
        $this->commands(GenerateSeederCommand::class);
        $this->commands(SkijasiFirebaseCommand::class);
        $this->commands(SkijasiTestSetup::class);
    }
}
