# Raven

[![Build Status](https://travis-ci.org/rcrowe/Raven.png?branch=master)](https://travis-ci.org/rcrowe/Raven)
[![Latest Stable Version](https://poser.pugx.org/rcrowe/Raven/v/stable.png)](https://packagist.org/packages/rcrowe/Raven)
[![Coverage Status](https://coveralls.io/repos/rcrowe/Raven/badge.png?branch=master)](https://coveralls.io/r/rcrowe/Raven?branch=master)
[![Total Downloads](https://poser.pugx.org/rcrowe/raven/downloads.png)](https://packagist.org/packages/rcrowe/raven)

Raven is a client for recording and transmitting messages to [Sentry](http://getsentry.com).

Its special sauce is that it can transmit those messages to Sentry in the background. No more slow down while a HTTP request is made!

Raven offers flexibility in how those messages are captured, processed & sent. But also offers quick seemless intergration into a range of frameworks, such as:

- [Laravel](https://github.com/rcrowe/Raven#laravel)

[![Sentry](https://www.getsentry.com/_static/getsentry/images/hero.png)](http://getsentry.com)

- [Installation](https://github.com/rcrowe/Raven#installation)
- [Usage](https://github.com/rcrowe/Raven#usage)
    - [Handlers](https://github.com/rcrowe/Raven#handlers)
        - [Sync](https://github.com/rcrowe/Raven#sync-handler)
        - [Laravel](https://github.com/rcrowe/Raven#laravel-handler)
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

Then follow the instructions for your [provider](https://github.com/rcrowe/Raven#providers) (if you are using one).

## Usage

This library exposes the same API for recording your messages as the official raven-php client. It should just be a case of
replacing `Raven_Client` with `rcrowe\Raven\Client`. For usage of recording messages checkout out [raven-php](https://github.com/getsentry/raven-php).

To record a message and transmit it to Sentry straight away (the default):

```php
$raven = new \rcrowe\Raven\Client(##DSN##);

$raven->captureMessage('FooBar');
```

### Handlers

Handlers are responsible for taking a new captured message and putting into a background queue. If no handler is registered with the
raven client the message is transmitted straight away.

A handler can be added to the client as follows:

```php
$raven = new \rcrowe\Raven\Client(##DSN##);

$raven->setHandler(
    new \rcrowe\Raven\Handler\Sync
);
```

##### Sync Handler

(Default) Like [raven-php](https://github.com/getsentry/raven-php) new messages are transmitted straight away.

##### Laravel Handler

If using within a Laravel project, makes use of the `illuminate\queue` API. For improved Laravel intergration checkout the [Laravel provider](#laravel) below.

```php
$raven = new \rcrowe\Raven\Client(##DSN##);

$raven->setHandler(
    new \rcrowe\Raven\Handler\Laravel(
        null,
        App::make('queue')
    );
);
```

### Transports

Transports are responsible for sending the message to the Sentry API. Transports are always the first parameter passed into a handler. If
no transport is provided it will default to HTTP.

```php
$raven->setHandler(
    new \rcrowe\Raven\Handler\Sync(
        new \rcrowe\Raven\Transport\Dummy
    )
);
```

##### Dummy

Dummy transport does absolutely nothing. Nothing is transmitted to the API. You may not want to transmit any messages when working in a dev environment.

#### HTTP

(Default) Transmit the message over HTTP. To do this we make use of the great HTTP client [Guzzle](http://guzzlephp.org/).

```php
$raven->setHandler(
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

## Providers

Providers offer painless integration to other libraries / frameworks.

### Laravel

#### Installation

Add the service provider to `app/config/app.php`:

```php
'rcrowe\Raven\Provider\Laravel\RavenServiceProvider',
```

Optionally register the facade to your aliases:

```php
'Sentry' => 'rcrowe\Raven\Provider\Laravel\Facade\Raven.php',
```

#### Configuration

Raven needs to know your client DSN. First publish the Raven config file with the following command:

```
php artisan config:publish rcrowe/raven
```

Then edit `app/config/packages/rcrowe/raven/config.php`

You can also set your Raven DSN from `app/config/services.php`:

```php
'raven' => [
	'dsn' => '...'
],
```

**Note:** Raven makes use of the Laravel queue, so make sure your `app/config/queue.php` is set correctly.

#### Usage

Now where ever you want to record a message just use the normal Log facade.

```php
try {
    throw new Exception('This is an example');
} catch (Exception $ex) {
    Log::error($ex);
}
```

To capture and send all messages you can add the following:

```php
App::error(function(Exception $exception, $code)
{
    Log::error($exception);
});
```

Using the alias you can set / remove the user information for all messages:

```php
Sentry::setUser([
	'id'   => 1,
	'name' => 'Rob Crowe',
]);

Sentry::removeUser();
```

**Note:** Check out the config file for more!