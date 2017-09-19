<?php

namespace Middlewares;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MethodOverride implements MiddlewareInterface
{
    const HEADER = 'X-Http-Method-Override';

    /**
     * @var array Allowed methods overrided in GET
     */
    private $get = ['HEAD', 'CONNECT', 'TRACE', 'OPTIONS'];

    /**
     * @var array Allowed methods overrided in POST
     */
    private $post = ['PATCH', 'PUT', 'DELETE', 'COPY', 'LOCK', 'UNLOCK'];

    /**
     * @var null|string The POST parameter name
     */
    private $parsedBodyParameter;

    /**
     * @var null|string The GET parameter name
     */
    private $queryParameter;

    /**
     * Set allowed method for GET.
     *
     * @return self
     */
    public function get(array $methods)
    {
        $this->get = $methods;

        return $this;
    }

    /**
     * Set allowed method for POST.
     *
     * @return self
     */
    public function post(array $methods)
    {
        $this->post = $methods;

        return $this;
    }

    /**
     * Configure the parameter using in GET requests.
     *
     * @param string $name
     *
     * @return self
     */
    public function queryParameter($name)
    {
        $this->queryParameter = $name;

        return $this;
    }

    /**
     * Configure the parameter using in POST requests.
     *
     * @param string $name
     *
     * @return self
     */
    public function parsedBodyParameter($name)
    {
        $this->parsedBodyParameter = $name;

        return $this;
    }

    /**
     * Process a server request and return a response.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        $method = $this->getOverrideMethod($request);

        if (!empty($method) && $method !== $request->getMethod()) {
            $allowed = $this->getAllowedOverrideMethods($request);

            if (!empty($allowed)) {
                if (in_array($method, $allowed)) {
                    $request = $request->withMethod($method);
                } else {
                    return Utils\Factory::createResponse(405);
                }
            }
        }

        return $handler->handle($request);
    }

    /**
     * Returns the override method.
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    private function getOverrideMethod(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST' && $this->parsedBodyParameter !== null) {
            $params = $request->getParsedBody();

            if (isset($params[$this->parsedBodyParameter])) {
                return strtoupper($params[$this->parsedBodyParameter]);
            }
        } elseif ($request->getMethod() === 'GET' && $this->queryParameter !== null) {
            $params = $request->getQueryParams();

            if (isset($params[$this->queryParameter])) {
                return strtoupper($params[$this->queryParameter]);
            }
        }

        return strtoupper($request->getHeaderLine(self::HEADER));
    }

    /**
     * Returns the allowed override methods.
     *
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    private function getAllowedOverrideMethods(ServerRequestInterface $request)
    {
        switch ($request->getMethod()) {
            case 'GET':
                return $this->get;
            case 'POST':
                return $this->post;
            default:
                return [];
        }
    }
}
