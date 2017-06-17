<?php

namespace Polass\Message;

use Illuminate\Support\ServiceProvider;
use Polass\Message\Message;
use Polass\Message\MessageManager;
use Polass\Message\SessionMessageRepository;

class MessageServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the message manager instance.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('polass.message', function($app) {
            return new Message;
        });

        $this->app->singleton('polass.message.manager', function($app) {
            return new MessageManager($app);
        });

        $this->app->singleton('polass.message.repository', function($app) {
            return new SessionMessageRepository($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ 'polass.message', 'polass.message.manager', 'polass.message.repository' ];
    }
}
