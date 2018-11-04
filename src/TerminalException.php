<?php

declare(strict_types=1);

namespace gamringer\Pipe;

use \Psr\Http\Message\ServerRequestInterface;

class TerminalException extends \Exception
{
    protected $request;
    
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;

        parent::__construct('Middleware queue exhausted');
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

}
