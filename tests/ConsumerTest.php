<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use gamringer\Pipe\Pipe;

class ConsumerTest extends TestCase
{
    public function testNestedPipePassesOverToNextMiddleware()
    {
        $request = new GuzzleHttp\Psr7\ServerRequest('GET', '/');

        $middleware = new gamringer\Pipe\Tests\Middlewares\NullMiddleware();
        $middleware2 = new gamringer\Pipe\Tests\Middlewares\StaticMiddleware();

        $pipe = new Pipe([$middleware]);
        $pipe2 = new Pipe([$pipe, $middleware2]);

        $this->assertSame($pipe2->handle($request), $middleware2->getResponse());
    }
}
