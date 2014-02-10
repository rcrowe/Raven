<?php

/**
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

namespace rcrowe\Raven\Provider\Laravel;

use Illuminate\Log\Writer;
use rcrowe\Raven\Client as Sentry;
use Exception;
use RuntimeException;
use Closure;

/**
 * Overrides default Logger to provide extra functionality.
 */
class Log extends Writer
{
    /**
     * @var string Log level for Log::exception
     *
     * @see \Illuminate\Log\Writer::levels
     * @see Log::exception()
     */
    protected $exceptionLevel = 'error';

    /**
     * @var \rcrowe\Raven\Client
     */
    protected $sentry;

    /**
     * Get the log level for Log::exception() calls.
     *
     * @return string
     */
    public function getExceptionLevel()
    {
        return $this->exceptionLevel;
    }

    /**
     * Set the log level used by Log::exception().
     *
     * @param string $level
     *
     * @return void
     */
    public function setExceptionLevel($level)
    {
        $this->exceptionLevel = $level;
    }

    /**
     * Get Sentry client.
     *
     * @return \rcrowe\Raven\Client
     */
    public function getSentry()
    {
        return $this->sentry;
    }

    /**
     * Set the sentry client.
     *
     * @param \rcrowe\Raven\Client $sentry
     *
     * @return void
     */
    public function setSentry(Sentry $sentry)
    {
        $this->sentry = $sentry;
    }

    /**
     * Include user details with logging.
     *
     * @param array $user User details.
     *
     * @return bool Whether user set successfully.
     */
    public function setUser(array $user)
    {
        if (empty($this->sentry)) {
            // Sentry logging not enabled
            return false;
        }

        $this->sentry->user_context($user);
        return true;
    }

    /**
     * Remove user details from log data.
     *
     * @return bool Whether user data successfully removed.
     */
    public function removeUser()
    {
        if (empty($this->sentry)) {
            // Sentry logging not enabled
            return false;
        }

        $this->sentry->user_context(array());
    }

    /**
     * Log an exception.
     *
     * Wrapper around normal log functions so you don't have to deal with context options.
     *
     * @param \Exception $exception
     * @param array      $options
     * @param level      $level     Override default exception log level.
     *
     * @throws \InvalidArgumentException Unknown log level.
     *
     * @return mixed
     */
    public function exception(Exception $exception, array $options = array(), $level = null)
    {
        $level   = (empty($level)) ? $this->exceptionLevel : $level;
        $message = $exception->getMessage();
        $context = array_merge($options, array(
            'exception' => $exception,
        ));

        call_user_func_array(array($this, $level), array($message, $context));
    }

    /**
     * Register a new Monolog handler.
     *
     * @param string   $level   Laravel log level.
     * @param \Closure $closure Return an instance of \Monolog\Handler\HandlerInterface.
     *
     * @throws \InvalidArgumentException Unknown log level.
     *
     * @return bool Whether handler was registered.
     */
    public function registerHandler($level, Closure $callback)
    {
        $level   = $this->parseLevel($level);
        $handler = call_user_func($callback, $level);

        // Add handler to Monolog
        $this->getMonolog()->pushHandler($handler);

        return true;
    }
}
