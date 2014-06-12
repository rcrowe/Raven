<?php

/*
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

return array(

    /*
    |--------------------------------------------------------------------------
    | Client DSN
    |--------------------------------------------------------------------------
    |
    | Your client DSN string. Find yours at https://app.getsentry.com/#team#/#project#/docs/.
    |
    */

    'dsn' => '',

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Whether logging is enabled. Useful for controlling logging per environment.
    |
    */

    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Log Level
    |--------------------------------------------------------------------------
    |
    | Log level (inclusive) at which to log to Sentry. Default `error`.
    |
    */

    'level' => 'error',

    /*
    |--------------------------------------------------------------------------
    | Monolog
    |--------------------------------------------------------------------------
    |
    | Customise the Monolog Raven handler.
    |
    */

    'monolog' => array(

        /*
        |--------------------------------------------------------------------------
        | Processors
        |--------------------------------------------------------------------------
        |
        | Set extra data on every log made to Sentry. Supports both closures & strings.
        | Monolog has a number of built-in processors which you can find here:
        |
        | https://github.com/Seldaek/monolog/blob/master/README.mdown#processors
        |
        | For more details check out the Monolog docs:
        | https://github.com/Seldaek/monolog/blob/master/doc/usage.md#using-processors
        |
        | For example:
        |
        | 'processors' => [
        |     'Monolog\Processor\GitProcessor',
        |     function ($record) {
        |         $record['extra']['dummy'] = 'Hello world';
        |         return $record;
        |     }
        | ]
        |
        */

        'processors' => array(
            // 'Monolog\Processor\GitProcessor'
        ),

    ),

);
