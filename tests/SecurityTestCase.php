<?php

abstract class SecurityTestCase extends \Orchestra\Testbench\BrowserKit\TestCase
{

    protected $facade;

    protected function getPackageProviders($app)
    {
        return ['Distilleries\Security\SecurityServiceProvider'];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->app['Illuminate\Contracts\Console\Kernel']->call('vendor:publish', ['--all' => true]);

        $this->app['router']->post('inputs', 'PostXssController@getIndex');

    }

    protected function getEnvironmentSetUp($app)
    {
        $app->make('Illuminate\Contracts\Http\Kernel')
            ->pushMiddleware('Distilleries\Security\Http\Middleware\XSS');

    }
}