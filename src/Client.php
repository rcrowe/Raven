<?php

/*
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

namespace rcrowe\Raven;

use Raven_Client;
use Raven_Compat;
use Closure;
use rcrowe\Raven\Handler\HandlerInterface;
use rcrowe\Raven\Handler\Sync;
use RuntimeException;
use Raven_Serializer;

class Client extends Raven_Client
{
    /**
     * @var string Client semver.
     */
    const VERSION = '0.2.0';

    /**
     * @var \rcrowe\Raven\Handler\HandlerInterface Background handler.
     */
    protected $handler;

    /**
     * @var \Closure Used to compress the JSON.
     */
    protected $encoder;

    /**
     * {@inheritdoc}
     */
    public function __construct($options_or_dsn = null, $options = [])
    {
        parent::__construct($options_or_dsn, $options);

        $this->logger  = 'rcrowe-raven/'.static::VERSION;
        $this->handler = new Sync;
        $this->encoder = function (array $data) {
            $message = Raven_Compat::json_encode($data);

            if (function_exists('gzcompress')) {
                $message = base64_encode(gzcompress($message));
            }

            return $message;
        };
    }

    /**
     * Get background handler.
     *
     * @return \rcrowe\Raven\Handler\HandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Set background handler.
     *
     * @param \rcrowe\Raven\Handler\HandlerInterface $handler
     *
     * @return void
     */
    public function setHandler(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Return closure used to compress data.
     *
     * @return \Closure
     */
    public function getEncoder()
    {
        return $this->encoder;
    }

    /**
     * Set closure used to compress data.
     *
     * @param \Closure $callback
     *
     * @return void
     */
    public function setEncoder(Closure $callback)
    {
        $this->encoder = $callback;
    }
    
    public function sanitize(&$data)
    {
        $app    = app();
        $config = $app->make('config');
        $files  = $app->make('files');

        // manually trigger autoloading, as it's not done in some edge cases due to PHP bugs (see #60149)
        if ( ! class_exists('Raven_Serializer'))
        {
            spl_autoload_call('Raven_Serializer');
        }
        
        // get keys variables environment file
        $configEnv = $config->getEnvironment();
        if ($configEnv == 'production')
        {
            $envFile = '.env.php';
        }
        else
        {
            $envFile = '.env.' . $configEnv . '.php';
        }

        $envFileComplete = base_path() . '/' . $envFile; 
        if ($files->exists($envFileComplete))
        {
            $envKeys = array_keys($files->getRequire($envFileComplete));

            // remove variables environment file in data send to sentry
            if (isset($data['sentry.interfaces.Http']['env']) && is_array($data['sentry.interfaces.Http']['env']))
            {
                foreach ($data['sentry.interfaces.Http']['env'] as $key => $value)
                {
                    if (in_array($key, $envKeys))
                    {
                        unset($data['sentry.interfaces.Http']['env'][$key]);
                    }
                }
            }
        }

        $data = Raven_Serializer::serialize($data);
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException Thrown when no sentry servers have been set.
     */
    public function send($data)
    {
        if (!$this->servers) {
            throw new RuntimeException('No Raven DSN set. Unable to log to Sentry.');
        }

        // Encode & compress data
        $message = call_user_func($this->encoder, $data);

        // Build up headers
        $client_string = 'rcrowe-raven/'.static::VERSION;
        $headers       = [
            'User-Agent'    => $client_string,
            'X-Sentry-Auth' => $this->get_auth_header(
                microtime(true),
                $client_string,
                $this->public_key,
                $this->secret_key
            ),
            'Content-Type' => 'application/octet-stream',
        ];

        foreach ($this->servers as $url) {
            $this->handler->process($url, $message, $headers);
        }

        return true;
    }
}
