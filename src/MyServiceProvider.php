<?php

namespace Mplacegit\Statistica;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelFilemanagerServiceProvider.
 */
class MyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
      
<<<<<<< HEAD
	          $this->publishes([
              __DIR__ . '/../config/mp-statistica.php' => config_path('mp-statistica.php'),
              ], 'config');

		  
=======
		    $this->mergeConfigFrom(__DIR__ . '../../config/module.php', 'module');
			$this->publishes([
                __DIR__.'../../config' => base_path('config'),
            ]);
            $modules = config("module.modules");
			#var_dump($modules);
            #include __DIR__ . '/routes.php';
>>>>>>> b74054ef66fd0f63808d9bf9f30aaefbf58e355f
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
<<<<<<< HEAD
        $this->mergeConfigFrom(__DIR__ . '/../config/mp-statistica.php', 'mp-statistica');
		$this->app->singleton('mp-stat', function ($app) {
            return $app->make(Advertise::class);
        });
=======

>>>>>>> b74054ef66fd0f63808d9bf9f30aaefbf58e355f
    }
}
