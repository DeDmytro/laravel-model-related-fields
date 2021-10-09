<?php

namespace Tests;

use DeDmytro\LaravelModelRelatedFields\ModelRelatedFieldsServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [ModelRelatedFieldsServiceProvider::class];
    }
}
