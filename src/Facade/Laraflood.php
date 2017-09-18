<?php

namespace Vincendev\Laraflood\Facade;

use Illuminate\Support\Facades\Facade;

class Laraflood extends Facade
{
    protected static function getFacadeAccessor() { return 'laraflood'; }
}