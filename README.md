# middlewares/method-override

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]
[![SensioLabs Insight][ico-sensiolabs]][link-sensiolabs]

Middleware to override the request method using the `X-Http-Method-Override` header. This is useful for clients unable to send other methods than GET and POST.

## Requirements

* PHP >= 7.0
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/method-override](https://packagist.org/packages/middlewares/method-override).

```sh
composer require middlewares/method-override
```

## Example

```php
$dispatcher = new Dispatcher([
	(new Middlewares\MethodOverride())
        ->getMethods(['HEAD', 'CONNECT', 'TRACE', 'OPTIONS'])
        ->postMethods(['PATCH', 'PUT', 'DELETE', 'COPY', 'LOCK', 'UNLOCK'])
		->queryParameter('method')
		->parsedBodyParameter('method')
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

## Options

#### `getMethods(array $methods)`

Array with the methods that can override the GET method. By default is `['HEAD', 'CONNECT', 'TRACE', 'OPTIONS']`.

#### `postMethods(array $methods)`

Array with the methods that can override the POST method. By default is `['PATCH', 'PUT', 'DELETE', 'COPY', 'LOCK', 'UNLOCK']`.

#### `queryParameter(string $name)`

Allows to use a query parameter in addition to the `X-Http-Method-Override` in GET requests.

#### `parsedBodyParameter(string $name)`

Allows to use a parsed body parameter in addition to the `X-Http-Method-Override` in POST.

#### `responseFactory(Psr\Http\Message\ResponseFactoryInterface $responseFactory)`

A PSR-17 factory to create `405` responses.

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/method-override.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/middlewares/method-override/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/middlewares/method-override.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/method-override.svg?style=flat-square
[ico-sensiolabs]: https://img.shields.io/sensiolabs/i/62857794-1977-49f7-aacb-35c1b0efc3a2.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/method-override
[link-travis]: https://travis-ci.org/middlewares/method-override
[link-scrutinizer]: https://scrutinizer-ci.com/g/middlewares/method-override
[link-downloads]: https://packagist.org/packages/middlewares/method-override
[link-sensiolabs]: https://insight.sensiolabs.com/projects/62857794-1977-49f7-aacb-35c1b0efc3a2
