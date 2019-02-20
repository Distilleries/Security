<?php

namespace Distilleries\Security;

class SecurityServiceProvider extends \Illuminate\Support\ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__.'/../../config/config.php'    => config_path('security.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../../config/config.php', 'security'
        );


    }

}