<?php

namespace Middlewares\Tests;

use Middlewares\MethodOverride;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;

class MethodOverrideTest extends \PHPUnit_Framework_TestCase
{
    public function headersProvider()
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
    public function testHeaders($original, $overrided, $status, $body)
    {
        $request = Factory::createServerRequest([], $original)
            ->withHeader('X-Http-Method-Override', $overrided);

        $response = Dispatcher::run([
            new MethodOverride(),
            function ($request) {
                echo $request->getMethod();
            },
        ], $request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals($body, (string) $response->getBody());
    }

    public function paramsProvider()
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
    public function testParams($original, $queryParams, $parsedBody, $status, $body)
    {
        $request = Factory::createServerRequest([], $original)
            ->withQueryParams($queryParams)
            ->withParsedBody($parsedBody);

        $response = Dispatcher::run([
            (new MethodOverride())
                ->parsedBodyParameter('method')
                ->queryParameter('method'),
            function ($request) {
                echo $request->getMethod();
            },
        ], $request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals($body, (string) $response->getBody());
    }
}
