<?php namespace Rjvim\Permiso;

use Illuminate\Support\Facades\Facade;

class PermisoFacade extends Facade 
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'permiso'; }

}