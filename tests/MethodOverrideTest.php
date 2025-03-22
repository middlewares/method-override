<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use Middlewares\MethodOverride;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class MethodOverrideTest extends TestCase
{
    /**
     * @return array<int, array<int, int|string>>
     */
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
    ): void {
        $response = Dispatcher::run(
            [
                new MethodOverride(),
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

    /**
     * @return array<int, array<int, int|string|array<string,string>>>
     */
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
     *
     * @param array<string,string> $queryParams
     * @param array<string,string> $parsedBody
     */
    public function testParams(
        string $original,
        array $queryParams,
        array $parsedBody,
        int $status,
        string $body
    ): void {
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

    public function testCustomGet(): void
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

    public function testCustomPost(): void
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
