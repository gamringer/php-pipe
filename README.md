Pipe
============

A PSR-15 handler with no assumption

#License
Pipe is licensed under the MIT license.

#Installation

    composer require gamringer/pipe

##Tests

    composer install
    phpunit

#Documentation

    $request = // Some PSR-7 ServerRequest object

    // Pipe can be created from a stack
    $pipe = new \gamringer\Pipe\Pipe([
        new \gamringer\Pipe\Example\FooMiddleware('1'),
    ]);

    // New middlewares can be stacked on top of existing ones
    $pipe->push(new \gamringer\Pipe\Example\StaticMiddleware(new \GuzzleHttp\Psr7\Response()));

    // Pipe responds to RequestHandler handle() method
    $response = $pipe->handle($request);

    // Pipe also responds to Middleware process() method such that it can be nested in a Middleware stack
    $pipe2 = new \gamringer\Pipe\Pipe();
    $pipe2->push($pipe);
    $response = $pipe2->handle($request);
