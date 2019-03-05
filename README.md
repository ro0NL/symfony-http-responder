# Symfony HTTP Responder

[![Build status][master:travis:img]][master:travis]
[![Latest Stable Version][packagist:img]][packagist]

[ADR][wiki:adr] implemented in a nutshell:

```php
<?php
namespace App\Controller;

use ro0NL\HttpResponder\Responder;
use ro0NL\HttpResponder\RespondTemplate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SomeController
{
    public function __invoke(Request $request, Responder $responder): Response
    {
        return $responder->respond(new RespondTemplate('home.html.twig'));
    }
}
```

## Work In Progress

The package development is not started yet. The repository is created after some positive feedback in the OSS community.

Inspired by https://github.com/msgphp/symfony-demo-app/tree/master/src/Http

Took a shot to port it to Symfony Core, see https://github.com/symfony/symfony/issues/29574. But here we are now.

# Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md)

[master:travis]: https://travis-ci.org/ro0NL/symfony-http-responder
[master:travis:img]: https://img.shields.io/travis/ro0NL/symfony-http-responder/master.svg?style=flat-square
[packagist]: https://packagist.org/packages/ro0NL/http-responder
[packagist:img]: https://img.shields.io/packagist/v/ro0NL/http-responder.svg?style=flat-square
[wiki:adr]: https://en.wikipedia.org/wiki/Action%E2%80%93domain%E2%80%93responder
