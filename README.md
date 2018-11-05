# Pipe

A PSR-15 handler with no assumption

## License
Pipe is licensed under the MIT license.

## Installation

```bash
$ composer require gamringer/pipe
````

### Tests

```bash
$ composer install
$ phpunit
```

## Documentation

```php
<?php
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

// If it runs out of middlewares to call, Pipe will respond differently depending on the way it was called.

// If called as a RequestHandler, when running out of Middlewares, it will throw a TerminalException
$pipe3 = new \gamringer\Pipe\Pipe();
$response = $pipe3->handle($request); // Throws new \gamringer\Pipe\TerminalException()

// If called as a Middleware, when running out of Middlewares itself, it will simply pass along to its next sibling
$staticresponse = new \GuzzleHttp\Psr7\Response();
$pipe4 = new \gamringer\Pipe\Pipe();
$pipe4->push(new \gamringer\Pipe\Pipe());
$pipe4->push(new \gamringer\Pipe\Example\StaticMiddleware($staticresponse));
$response = $pipe4->handle($request); // returns $staticresponse
```
