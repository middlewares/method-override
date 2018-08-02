<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use Middlewares\MethodOverride;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class MethodOverrideTest extends TestCase
{
    public function headersProvider(): array
    {
        return [
            ['GET', 'HEAD', 200, 'HEAD'],
            ['POST', 'HEAD', 405, ''],
            ['GET', 'POST', 405, ''],
            ['GET', 'GET', 200, 'GET'],
        ];
    }

    /**
     * @dataProvider headersProvider
     */
    public function testHeaders(
        string $original,
        string $overrided,
        int $status,
        string $body
    ) {
        $response = Dispatcher::run(
            [
                (new MethodOverride())->responseFactory(new \Middlewares\Utils\Factory\DiactorosFactory()),
                function ($request) {
                    echo $request->getMethod();
                },
            ],
            Factory::createServerRequest($original, '/')
                ->withHeader('X-Http-Method-Override', $overrided)
        );

        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals($body, (string) $response->getBody());
    }

    public function paramsProvider(): array
    {
        return [
            ['GET', [], [], 200, 'GET'],
            ['GET', ['method' => 'head'], [], 200, 'HEAD'],
            ['GET', ['method' => 'PUT'], [], 405, ''],
            ['POST', ['method' => 'PUT'], [], 200, 'POST'],
            ['POST', [], ['method' => 'PUT'], 200, 'PUT'],
            ['POST', [], ['method' => 'GET'], 405, ''],
        ];
    }

    /**
     * @dataProvider paramsProvider
     */
    public function testParams(
        string $original,
        array $queryParams,
        array $parsedBody,
        int $status,
        string $body
    ) {
        $middleware = (new MethodOverride())
            ->parsedBodyParameter('method')
            ->queryParameter('method');

        $response = Dispatcher::run(
            [
                $middleware,
                function ($request) {
                    echo $request->getMethod();
                },
            ],
            Factory::createServerRequest($original, '/')
                ->withQueryParams($queryParams)
                ->withParsedBody($parsedBody)
        );

        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals($body, (string) $response->getBody());
    }

    public function testCustomGet()
    {
        $response = Dispatcher::run(
            [
                (new MethodOverride())->getMethods(['CONNECT']),
            ],
            Factory::createServerRequest('GET', '/')
                ->withHeader('X-Http-Method-Override', 'HEAD')
        );

        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testCustomPost()
    {
        $response = Dispatcher::run(
            [
                (new MethodOverride())->postMethods(['PUT']),
            ],
            Factory::createServerRequest('POST', '/')
                ->withHeader('X-Http-Method-Override', 'DELETE')
        );

        $this->assertEquals(405, $response->getStatusCode());
    }
}
