<?php

namespace Mplacegit\Statistica;

use Illuminate\Support\Facades\Facade;

class AdvertiseFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mp-stat';
    }
}

