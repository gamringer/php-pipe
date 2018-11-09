# Pipe

A PSR-15 handler with no assumption

## License
Pipe is licensed under the MIT license.

## Installation

```bash
$ composer require gamringer/pipe
```

### Tests

```bash
$ composer install
$ phpunit
```

## Documentation

These are all the various ways in which Pipe can be used. These examples can be found in the examples directory.
First, let the following code simply be assumed present in all examples:

```php
<?php

declare(strict_types=1);

include __DIR__.'/../vendor/autoload.php';

use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use gamringer\Pipe\Pipe;
use gamringer\Pipe\Example\StaticMiddleware;

// Request to be handled later
$request = new ServerRequest('GET', '/');

// For use in this example, we build a few Middleware objects

// One that simply returns a predetermined response in all cases
$staticResponse = new Response();
$staticResponseMiddleware = new StaticMiddleware($staticResponse);

// One that echoes it's constructor argument before passing to the next one.
$fooMiddleware = new FooMiddleware('1');

```

### Create pipe from an array of Middlewares
```php
// Build Pipe from an array of Middlewares
$pipe = new Pipe([
    $staticResponseMiddleware,
]);

// Use Pipe via RequestHandlerInterface
$response = $pipe->handle($request);
```
### Create an empty pipe and add Middlewares dynamically
```php
// Build empty Pipe
$pipe = new Pipe();

// Add Middlewares dynamically
$pipe->push($staticResponseMiddleware);

// Use Pipe via RequestHandlerInterface
$response = $pipe->handle($request);
```
### Stack many Middlewares dynamically
```php
// Build empty Pipe
$pipe = new Pipe();

// Add Middlewares dynamically
$pipe->push($fooMiddleware);
$pipe->push($staticResponseMiddleware);

// Use Pipe via RequestHandlerInterface
$response = $pipe->handle($request);
```
### Use pipe as a Middleware
Pipe can also be used via the MiddlewareInterface by pushing it into another Pipe
```php
// Build empty Pipe
$pipe = new Pipe();

// Add Middlewares dynamically
$pipe->push($fooMiddleware);
$pipe->push(new Pipe([
    new FooMiddleware('2') 
]));
$pipe->push($staticResponseMiddleware);

// Use Pipe via RequestHandlerInterface
$response = $pipe->handle($request);
```
### Running out of Middlewares
If it runs out of middlewares to call, Pipe will respond differently depending on the way it was called.

#### As a RequestHandler
Most applications will be using Pipe as a RequestHandler to stack Middlewares and handle user requests. If the request goes through the Pipe and the last Middleware attempts to delegate to the next one, a TerminalException is raised. The same logic also covers the empty Pipe case: 
```php
$pipe = new Pipe();
$pipe->handle($request); // Throws new \gamringer\Pipe\TerminalException()
```

#### As a Middleware
If called as a Middleware, when running out of Middlewares itself, it will simply pass along to its next sibling:
```php
// Build empty Pipe
$pipe = new Pipe();

// Add Middlewares dynamically
$pipe->push($fooMiddleware);
$pipe->push(new Pipe([
    new FooMiddleware('2')
]));
$pipe->push(new Pipe());
$pipe->push($staticResponseMiddleware);

// Use Pipe via RequestHandlerInterface
$response = $pipe->handle($request);

```
