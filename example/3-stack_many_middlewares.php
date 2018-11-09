<?php

declare(strict_types=1);

include __DIR__.'/../vendor/autoload.php';

use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use gamringer\Pipe\Pipe;
use gamringer\Pipe\Example\StaticMiddleware;
use gamringer\Pipe\Example\FooMiddleware;

// Request to be handled later
$request = new ServerRequest('GET', '/');

// For use in this example, we build a few Middleware objects

// One that simply returns a predetermined response in all cases
$staticResponse = new Response();
$staticResponseMiddleware = new StaticMiddleware($staticResponse);

// One that echoes it's constructor argument before passing to the next one.
$fooMiddleware = new FooMiddleware('1');

// Build empty Pipe
$pipe = new Pipe();

// Add Middlewares dynamically
$pipe->push($fooMiddleware);
$pipe->push($staticResponseMiddleware);

// Use Pipe via RequestHandlerInterface
$response = $pipe->handle($request);

// Dump
var_dump($response);
