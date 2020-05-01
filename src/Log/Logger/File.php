<?php


namespace Steed\Log\Logger;


class File
{
    public function write($level, $message, array $context = array())
    {

        error_log($message, 3, RUNTIME_PATH . DIRECTORY_SEPARATOR . 'log');

    }
}