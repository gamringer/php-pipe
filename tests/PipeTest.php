<?php

declare(strict_types=1);

namespace gamringer\Pipe\Tests;

use PHPUnit\Framework\TestCase;
use gamringer\Pipe\Pipe;

class PipeTest extends TestCase
{
    public function testSingleStaticMiddlewareReturnsAResponse()
    {
        $request = new \GuzzleHttp\Psr7\ServerRequest('GET', '/');

        $middleware = new \gamringer\Pipe\Tests\Middlewares\StaticMiddleware();

        $pipe = new Pipe([$middleware]);
        $this->assertSame($pipe->handle($request), $middleware->getResponse());
    }

    /**
     * @dataProvider validInitialStackProvider
     */
    public function testCreateFromAnyIterable($expectedResponse, $validInitialStack)
    {
        $request = new \GuzzleHttp\Psr7\ServerRequest('GET', '/');

        $pipe = new Pipe($validInitialStack);
        $this->assertSame($pipe->handle($request), $expectedResponse);
    }

    /**
     * @dataProvider invalidMiddlewareProvider
     */
    public function testOnlyCreateFromMiddlewareInterfaces($invalidMiddleware)
    {
        $this->expectException(\TypeError::class);

        new Pipe([$invalidMiddleware]);
    }

    /**
     * @dataProvider invalidMiddlewareProvider
     */
    public function testOnlyAddMiddlewareInterfaces($invalidMiddleware)
    {
        $this->expectException(\TypeError::class);

        $pipe = new Pipe();
        $pipe->push($invalidMiddleware);
    }

    public function validInitialStackProvider()
    {
        $middleware = new \gamringer\Pipe\Tests\Middlewares\StaticMiddleware();
        $expectedResponse = $middleware->getResponse();

        $dll = new \SplDoublyLinkedList();
        $dll->push($middleware);

        return [
            'array' => [$expectedResponse, [$middleware]],
            'SplDoublyLinkedList' => [$expectedResponse, $dll],
        ];
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
        $request = new \GuzzleHttp\Psr7\ServerRequest('GET', '/');

        $middleware = new \gamringer\Pipe\Tests\Middlewares\StaticMiddleware();
        $middleware2 = new \gamringer\Pipe\Tests\Middlewares\StaticMiddleware();

        $pipe = new Pipe([$middleware, $middleware2]);
        $this->assertSame($pipe->handle($request), $middleware->getResponse());

        $pipe = new Pipe([$middleware2, $middleware]);
        $this->assertSame($pipe->handle($request), $middleware2->getResponse());
    }

    public function testPipeCanBeNestedAsAMiddleware()
    {
        $request = new \GuzzleHttp\Psr7\ServerRequest('GET', '/');

        $middleware = new \gamringer\Pipe\Tests\Middlewares\StaticMiddleware();

        $pipe = new Pipe([$middleware]);
        $pipe2 = new Pipe([$pipe]);
        $this->assertSame($pipe2->handle($request), $middleware->getResponse());
    }

    public function testEmptyPipeThrowsTerminalException()
    {
        $request = new \GuzzleHttp\Psr7\ServerRequest('GET', '/');

        $pipe = new Pipe();

        $this->expectException(\gamringer\Pipe\TerminalException::class);
        $pipe->handle($request);
    }

    public function testThrownTerminalExceptionContainsOriginalRequest()
    {
        $request = new \GuzzleHttp\Psr7\ServerRequest('GET', '/');

        $pipe = new Pipe();

        try {
            $pipe->handle($request);
            $this->fail();
        } catch (\gamringer\Pipe\TerminalException $e) {
            $this->assertSame($request, $e->getRequest());
        }
    }

    public function testUnendingPipeThrowsTerminalException()
    {
        $request = new \GuzzleHttp\Psr7\ServerRequest('GET', '/');

        $middleware = new \gamringer\Pipe\Tests\Middlewares\NullMiddleware();

        $pipe = new Pipe([$middleware]);

        $this->expectException(\gamringer\Pipe\TerminalException::class);
        $pipe->handle($request);
    }
}
