<?php

namespace Steed\Container;

use Steed\Contracts\Foundation\Application as ApplicationContract;

class Application implements ApplicationContract
{

    /**
     * The Steed framework version.
     *
     * @var string
     */
    const VERSION = '0.0.1';


    public function __construct()
    {
    }


    protected function initialize()
    {

    }


}