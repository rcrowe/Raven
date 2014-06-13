<?php

/**
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

namespace rcrowe\Raven\Provider\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Static interface to the Raven client.
 */
class Raven extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'log.raven';
    }

    /**
     * Include user details with logging.
     *
     * @param array $user User details.
     *
     * @return void
     */
    public static function setUser(array $user)
    {
        static::$app['log.raven']->user_context($user);
    }

    /**
     * Remove user details from log data.
     *
     * @return void
     */
    public static function removeUser()
    {
        static::$app['log.raven']->user_context([]);
    }
}
