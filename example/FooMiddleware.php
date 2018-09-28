<?php

declare(strict_types=1);

namespace gamringer\Pipe\Example;

use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Server\RequestHandlerInterface;

class FooMiddleware implements MiddlewareInterface
{
    protected $v;

    public function __construct($v)
    {
        $this->v = $v;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        echo 'A' . $this->v . PHP_EOL;
        $resp =  $handler->handle($request);
        echo 'B' . $this->v . PHP_EOL;

        return $resp;
    }
}
