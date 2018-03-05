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
      
		    $this->mergeConfigFrom(__DIR__ . '../../config/module.php', 'module');
			$this->publishes([
                __DIR__.'../../config' => base_path('config'),
            ]);
            $modules = config("module.modules");
			#var_dump($modules);
            #include __DIR__ . '/routes.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
