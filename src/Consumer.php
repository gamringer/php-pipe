<?php

declare(strict_types=1);

namespace gamringer\Pipe;

use \Psr\Http\Server\RequestHandlerInterface;
use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\ServerRequestInterface;

class Consumer implements RequestHandlerInterface
{

    protected $generator;
    protected $delegate;

    function __construct(Pipe $queue, RequestHandlerInterface $delegate = null)
    {
        $this->generator = $queue->getGenerator();
        $this->delegate = $delegate;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->generator->valid()) {
            return $this->delegate($request);
        }

        $current = $this->generator->current();
        $this->generator->next();

        return $current->process($request, $this);
    }

    private function delegate(ServerRequestInterface $request)
    {
        if ($this->delegate === null) {
            throw new TerminalException($request);
        }

        return $this->delegate->handle($request);
    }
}
