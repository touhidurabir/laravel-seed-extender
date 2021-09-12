<?php

namespace Touhidurabir\SeedExtender\Facades;

use Illuminate\Support\Facades\Facade;

class SeedExtender extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {

        return 'seed-extender';
    }
}