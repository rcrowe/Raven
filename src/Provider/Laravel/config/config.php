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
    | Exception Log Level
    |--------------------------------------------------------------------------
    |
    | Log level to use whening using `Log::exception()`.
    |
    | You can override the level per call with the 3rd parameter. For example:
    | Log::exception($ex, $context, $level)
    |
    */

    'exceptionLevel' => 'error',


);
