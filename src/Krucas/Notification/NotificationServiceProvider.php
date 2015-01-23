<?php namespace Krucas\Notification;

use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['events']->fire('notification.booted', $this->app['notification']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['config']->set('notification', require __DIR__.'/../config/config.php');

        $this->app['notification'] = $this->app->share(function ($app) {
                $config = $app['config'];

                $notification = new Notification(
                    $config->get('notification.default_container'),
                    $config->get('notification.default_types'),
                    $config->get('notification.default_format'),
                    $config->get('notification.default_formats')
                );

                $notification->setEventDispatcher($app['events']);

                return $notification;
            });

        $this->app->bind('Krucas\Notification\Subscriber', function ($app) {
                return new Subscriber($app['session'], $app['config']);
            });

        $this->app['events']->subscribe('Krucas\Notification\Subscriber');
    }
}