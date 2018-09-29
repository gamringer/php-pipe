<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use gamringer\Pipe\Pipe;

class PipeTest extends TestCase
{
    public function testSingleStaticMiddlewareReturnsAResponse()
    {
        $request = new GuzzleHttp\Psr7\ServerRequest('GET', '/');

        $middleware = new gamringer\Pipe\Tests\Middlewares\StaticMiddleware();

        $pipe = new Pipe([$middleware]);
        $this->assertSame($pipe->handle($request), $middleware->getResponse());
    }

    /**
     * @dataProvider invalidMiddlewareProvider
     */
    public function testOnlyCreateFromMiddlewareInterfaces($invalidMiddleware)
    {
        $this->expectException(TypeError::class);

        $pipe = new Pipe([$invalidMiddleware]);
    }

    /**
     * @dataProvider invalidMiddlewareProvider
     */
    public function testOnlyAddMiddlewareInterfaces($invalidMiddleware)
    {
        $this->expectException(TypeError::class);

        $pipe = new Pipe();
        $pipe->push($invalidMiddleware);
    }

    public function invalidMiddlewareProvider()
    {
        return [
            'null' => [null],
            'boolean true' => [true],
            'boolean false' => [false],
            'resource' => [fopen('php://memory', 'w+')],
            'array' => [[]],
            'stdClass' => [(object) []],
            'string empty' => [''],
            'string short' => ['a'],
            'string longer' => ['some longer string'],
            'string numeric' => ['1'],
            'float' => [0.1],
            'integer 1' => [1],
            'integer 0' => [0],
            'integer PHP_INT_MIN' => [PHP_INT_MIN],
            'integer PHP_INT_MAX' => [PHP_INT_MAX],
        ];
    }

    public function testMiddlewareOrderMatters()
    {
        $request = new GuzzleHttp\Psr7\ServerRequest('GET', '/');

        $middleware = new gamringer\Pipe\Tests\Middlewares\StaticMiddleware();
        $middleware2 = new gamringer\Pipe\Tests\Middlewares\StaticMiddleware();

        $pipe = new Pipe([$middleware, $middleware2]);
        $this->assertSame($pipe->handle($request), $middleware->getResponse());

        $pipe = new Pipe([$middleware2, $middleware]);
        $this->assertSame($pipe->handle($request), $middleware2->getResponse());
    }
}
