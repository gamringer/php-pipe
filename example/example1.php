<?php

declare(strict_types=1);

include __DIR__.'/../vendor/autoload.php';

$request = new \GuzzleHttp\Psr7\ServerRequest('GET', '/');

$pipe = new \gamringer\Pipe\Pipe([
    new \gamringer\Pipe\Example\FooMiddleware('1'),
    new \gamringer\Pipe\Example\FooMiddleware('2'),
    new \gamringer\Pipe\Pipe([
        new \gamringer\Pipe\Example\FooMiddleware('3'),
        new \gamringer\Pipe\Pipe([
            new \gamringer\Pipe\Example\FooMiddleware('4'),
            new \gamringer\Pipe\Example\FooMiddleware('5'),
            new \gamringer\Pipe\Example\FooMiddleware('6'),
        ]),
        new \gamringer\Pipe\Example\FooMiddleware('7'),
    ]),
]);
$pipe->push(new \gamringer\Pipe\Example\StaticMiddleware(new \GuzzleHttp\Psr7\Response()));

$response = $pipe->handle($request);

var_dump($response);
