<?php
declare(strict_types = 1);

namespace Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MethodOverride implements MiddlewareInterface
{
    const HEADER = 'X-Http-Method-Override';

    /**
     * @var array Allowed methods overrided in GET
     */
    private $getMethods = ['HEAD', 'CONNECT', 'TRACE', 'OPTIONS'];

    /**
     * @var array Allowed methods overrided in POST
     */
    private $postMethods = ['PATCH', 'PUT', 'DELETE', 'COPY', 'LOCK', 'UNLOCK'];

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
     */
    public function getMethods(array $getMethods): self
    {
        $this->getMethods = $getMethods;

        return $this;
    }

    /**
     * Set allowed method for POST.
     */
    public function postMethods(array $postMethods): self
    {
        $this->postMethods = $postMethods;

        return $this;
    }

    /**
     * Configure the parameter using in GET requests.
     */
    public function queryParameter(string $name): self
    {
        $this->queryParameter = $name;

        return $this;
    }

    /**
     * Configure the parameter using in POST requests.
     */
    public function parsedBodyParameter(string $name): self
    {
        $this->parsedBodyParameter = $name;

        return $this;
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
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
     */
    private function getOverrideMethod(ServerRequestInterface $request): string
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
     * @codeCoverageIgnore
     */
    private function getAllowedOverrideMethods(ServerRequestInterface $request): array
    {
        switch ($request->getMethod()) {
            case 'GET':
                return $this->getMethods;
            case 'POST':
                return $this->postMethods;
            default:
                return [];
        }
    }
}
