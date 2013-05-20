<?php namespace Fideloper\ConditionalRequest;

use Illuminate\Support\ServiceProvider;
use Fideloper\ConditionalRequest\Request\Symfony;
use Fideloper\ConditionalRequest\Conditional;

class ConditionalRequestServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Bind Resource objects to App IoC
        $this->app['symfonyrequest'] = $this->app->share(function($app)
        {
             return new Symfony( $app['request'] );
        });

        $this->app['conditionalrequest'] = $this->app->share(function($app)
        {
            return new Conditional( $app['symfonyrequest'] );
        });

    }

}