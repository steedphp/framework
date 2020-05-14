<?php

namespace Steed\Framework\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface as PsrNotFoundExceptionInterface;

class NotFoundExceptionInterface extends Exception implements PsrNotFoundExceptionInterface
{
    //
}
