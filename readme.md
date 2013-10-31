# Raven

Raven is a client for recording and transmitting messages to [Sentry](https://getsentry.com).

Its special sauce is that it can transmit those messages to Sentry in the background. No more slow down while a HTTP request is made!

- [Installation](https://github.com/rcrowe/Raven#installation)
- [Usage](https://github.com/rcrowe/Raven#usage)
    - [Handlers](https://github.com/rcrowe/Raven#handlers)
        - [Sync](https://github.com/rcrowe/Raven#sync-handler)
        - [Laravel](https://github.com/rcrowe/Raven#laravel-handler)
        - [Resque](https://github.com/rcrowe/Raven#resque-handler)
    - [Transports](https://github.com/rcrowe/Raven#transports)
        - [Dummy](https://github.com/rcrowe/Raven#dummy)
        - [Http](https://github.com/rcrowe/Raven#http)
        - [Udp](https://github.com/rcrowe/Raven#udp)
- [Providers](https://github.com/rcrowe/Raven#providers)
    - [Laravel](https://github.com/rcrowe/Raven#laravel)

## Installation

Add `rcrowe\raven` as a requirement to composer.json:

```javascript
{
    "require": {
        "rcrowe/raven": "dev-master"
    }
}
```

Update your packages with `composer update` or install with `composer install`.

## Usage

This library exposes the same API for recording your messages as the official raven-php client. It should just be a case of
replacing `Raven_Client` with `rcrowe\Raven\Client`. For usage of recording messages checkout out [raven-php](https://github.com/getsentry/raven-php).

To record a message and transmit it to Sentry straight away (the default):

```php
$raven = new \rcrowe\Raven\Client(##DSN##);

$raven->captureMessage('FooBar');
```

### Handlers

Handlers are responsible for taking a new captured raven message and putting into a background queue. If no handler is registered with the
raven client the message is transmitted straight away.

A handler can be added to the client as follows:

```php
$raven = new \rcrowe\Raven\Client(##DSN##);

$raven->addHandler(
    new \rcrowe\Raven\Handler\Sync
);
```

#### Sync Handler

(Default) Like [raven-php](https://github.com/getsentry/raven-php) new messages are transmitted straight away.

#### Laravel Handler

If using within a Laravel project, makes use of the `illuminate\queue` API. For improved Laravel intergration checkout the [Laravel provider](#laravel) below.

```php
$raven = new \rcrowe\Raven\Client(##DSN##);

$raven->addHandler(
    new \rcrowe\Raven\Handler\Laravel(
        null,
        App::make('queue')
    );
);
```
#### Resque Handler

`TODO` - Will be based around [https://github.com/chrisboulton/php-resque](https://github.com/chrisboulton/php-resque)

### Transports

Transports are responsible for sending the message to the Sentry API. Transports are always the first parameter passed into a handler. If
no transport is provided it will default to HTTP.

```php
$raven->addHandler(
    new \rcrowe\Raven\Handler\Sync(
        new \rcrowe\Raven\Transport\Dummy
    )
);
```

##### Dummy

Dummy transport does absolutely nothing. Nothing is transmitted to the API. You may not want to transmit to any messages when working
in a dev environment.

#### HTTP

(Default) Transmit the message over HTTP. To do this we make use of the great HTTP client [Guzzle](http://guzzlephp.org/).

```php
$raven->addHandler(
    new \rcrowe\Raven\Handler\Sync(
        new \rcrowe\Raven\Transport\Guzzle
    )
);
```

As this is the default transport mechanism there is no need to pass it into the handler. The above call is the same as:

```php
$raven->addHandler(
    new \rcrowe\Raven\Handler\Sync
);
```

#### UDP

`TODO`

## Providers

Providers offer painless integration to other libraries / frameworks.

### Laravel

#### Installation

Raven needs to know your client DSN. First publish the Raven config file with the following command:

```
php artisan config:publish rcrowe/raven --path vendor/rcrowe/raven/src/Provider/Laravel/config/
```

Then edit `app/config/packages/rcrowe/raven/config.php`

Now add the RavenServiceProvider to `app/config/app.php`:

```
rcrowe\Raven\Provider\Laravel\RavenServiceProvider
```

Make sure `app/config/queue.php` is setup with how you want to connect to your background queue. For further information on working
with queues in Laravel checkout their [docs](http://laravel.com/docs/queues).

#### Usage

Now where ever you want to record a message just use the normal Log facade.

```php
try {
    throw new Exception('This is an example');
} catch (Exception $ex) {
    Log::error($ex);
}
```
