# Symfony HTTP Responder

[![Build status][master:travis:img]][master:travis]
[![Latest Stable Version][packagist:img]][packagist]

[ADR][wiki:adr] implemented in a nutshell. A viable alternative to [`AbstractController`][sf:controller], or most base
controllers really.

```php
<?php

use ro0NL\HttpResponder\Bridge\Twig\RespondTemplate;
use ro0NL\HttpResponder\Responder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SomeHttpAction
{
    public function __invoke(Request $request, Responder $responder): Response
    {
        return $responder->respond(new RespondTemplate('home.html.twig'));
    }
}
```

# Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md)

[master:travis]: https://travis-ci.org/ro0NL/symfony-http-responder
[master:travis:img]: https://img.shields.io/travis/ro0NL/symfony-http-responder/master.svg?style=flat-square
[packagist]: https://packagist.org/packages/ro0NL/http-responder
[packagist:img]: https://img.shields.io/packagist/v/ro0NL/http-responder.svg?style=flat-square
[wiki:adr]: https://en.wikipedia.org/wiki/Action%E2%80%93domain%E2%80%93responder
[sf:controller]: https://github.com/symfony/symfony/blob/master/src/Symfony/Bundle/FrameworkBundle/Controller/AbstractController.php
