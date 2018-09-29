<?php

declare(strict_types=1);

namespace gamringer\Pipe\Tests\Middlewares;

use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Server\RequestHandlerInterface;

class StaticMiddleware implements MiddlewareInterface
{
    private $response;

    public function __construct()
    {
        $this->response = new \GuzzleHttp\Psr7\Response();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->response;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
