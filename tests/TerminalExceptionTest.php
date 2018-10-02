<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use gamringer\Pipe\Pipe;

class TerminalExceptionTest extends TestCase
{
    public function testSingleStaticMiddlewareReturnsAResponse()
    {
        $request = new GuzzleHttp\Psr7\ServerRequest('GET', '/');

        $exception = new \gamringer\Pipe\TerminalException($request);

        $this->assertSame($request, $exception->getRequest());
    }
}
