<?php

namespace Steed\Http;


class UrlParser
{
    public static function pathInfo($path)
    {
        $basePath = dirname($path);
        $info = pathInfo($path);
        if ($info['filename'] != 'index') {
            if ($basePath == '/') {
                $basePath = $basePath . $info['filename'];
            } else {
                $basePath = $basePath . '/' . $info['filename'];
            }
        }
        return $basePath;
    }
}