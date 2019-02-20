<?php namespace Rjvim\Permiso;

use Illuminate\Support\ServiceProvider;

class PermisoServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		// $config     = realpath(__DIR__.'/../../../config/rjvim.permiso.php');
		$migrations = realpath(__DIR__.'/../../migrations');

		// $this->mergeConfigFrom($config, 'rjvim.permiso');

		$this->publishes([
			// $config     => config_path('rjvim.permiso.php'),
			$migrations => $this->app->databasePath().'/migrations',
		]);
		
	    $this->app->singleton('permiso',function($app)
	    {
        	$permiso = new Permiso();
        	return $permiso;
	    });

	}


}
