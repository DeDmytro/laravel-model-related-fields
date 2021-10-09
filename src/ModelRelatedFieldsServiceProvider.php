<?php

namespace DeDmytro\LaravelModelRelatedFields;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

final class ModelRelatedFieldsServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->bootPublishes();
    }

    public function register(): void
    {
        $this->registerConfig();
    }

    protected function bootPublishes(): void
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-model-related-fields.php' => $this->app->configPath('laravel-model-related-fields.php'),
        ], 'config');
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-model-related-fields.php', 'laravel-model-related-fields');
    }
}
