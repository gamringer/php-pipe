<?php

declare(strict_types=1);

namespace gamringer\Pipe;

use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Server\RequestHandlerInterface;

class Pipe implements MiddlewareInterface, RequestHandlerInterface
{
    protected $content = [];

    function __construct(array $initialStack)
    {
        foreach ($initialStack as $item) {
            $this->push($item);
        }
    }

    public function push(MiddlewareInterface $value): void
    {
        $this->content[] = $value;
    }

    public function getGenerator(): \Generator
    {
        foreach ($this->content as $middleware) {
            yield $middleware;
        }
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return (new Consumer($this, $handler))->handle($request);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $consumer = new Consumer($this);

        return $consumer->handle($request);
    }
}
