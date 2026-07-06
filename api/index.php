<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (getenv('VERCEL')) {
    $storage = '/tmp/storage';
    foreach (['framework/cache/data', 'framework/sessions', 'framework/views', 'logs'] as $directory) {
        if (! is_dir($storage.'/'.$directory)) {
            mkdir($storage.'/'.$directory, 0777, true);
        }
    }
    putenv('LARAVEL_STORAGE_PATH='.$storage);
}

require __DIR__.'/../vendor/autoload.php';

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
