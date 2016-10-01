# middlewares/method-override

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]
[![SensioLabs Insight][ico-sensiolabs]][link-sensiolabs]

Middleware to override the request method using the `X-Http-Method-Override` header. This is useful for clients unable to send other methods than GET and POST.

**Note:** This middleware is intended for server side only

## Requirements

* PHP >= 5.6
* A [PSR-7](https://packagist.org/providers/psr/http-message-implementation) http mesage implementation ([Diactoros](https://github.com/zendframework/zend-diactoros), [Guzzle](https://github.com/guzzle/psr7), [Slim](https://github.com/slimphp/Slim), etc...)
* A [PSR-15](https://github.com/http-interop/http-middleware) middleware dispatcher ([Middleman](https://github.com/mindplay-dk/middleman), etc...)

## Installation

This package is installable and autoloadable via Composer as [middlewares/method-override](https://packagist.org/packages/middlewares/method-override).

```sh
composer require middlewares/method-override
```

## Example

```php
$dispatcher = new Dispatcher([
	(new Middlewares\MethodOverride())
        ->get(['HEAD', 'CONNECT', 'TRACE', 'OPTIONS'])
        ->post(['PATCH', 'PUT', 'DELETE', 'COPY', 'LOCK', 'UNLOCK'])
		->queryParameter('method')
		->parsedBodyParameter('method')
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

## Options

#### `get(array $methods)`

Array with the methods that can override the GET method. By default is `['HEAD', 'CONNECT', 'TRACE', 'OPTIONS']`.

#### `post(array $methods)`

Array with the methods that can override the POST method. By default is `['PATCH', 'PUT', 'DELETE', 'COPY', 'LOCK', 'UNLOCK']`.

#### `queryParameter(string $name)`

Allows to use a query parameter in addition to the `X-Http-Method-Override` in GET requests.

#### `parsedBodyParameter(string $name)`

Allows to use a parsed body parameter in addition to the `X-Http-Method-Override` in POST.

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/method-override.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/middlewares/method-override/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/middlewares/method-override.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/method-override.svg?style=flat-square
[ico-sensiolabs]: https://img.shields.io/sensiolabs/i/36786f5a-2a15-4399-8817-8f24fcd8c0b4.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/method-override
[link-travis]: https://travis-ci.org/middlewares/method-override
[link-scrutinizer]: https://scrutinizer-ci.com/g/middlewares/method-override
[link-downloads]: https://packagist.org/packages/middlewares/method-override
[link-sensiolabs]: https://insight.sensiolabs.com/projects/36786f5a-2a15-4399-8817-8f24fcd8c0b4
