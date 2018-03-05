<?php

namespace Mplacegit\Statistica;

use Illuminate\Support\Facades\Facade;

class Advertise extends Facade
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

