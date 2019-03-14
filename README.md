# Symfony HTTP Responder

[![Build status][master:travis:img]][master:travis]
[![Latest Stable Version][packagist:img]][packagist]

[ADR][wiki:adr] implemented in a nutshell. A viable alternative for [`AbstractController`][sf:controller], or most base
controllers really.

```php
use ro0NL\HttpResponder\Bridge\Twig\Template;
use ro0NL\HttpResponder\Responder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SomeHttpAction
{
    public function __invoke(Request $request, Responder $responder): Response
    {
        return $responder->respond(new Template('home.html.twig'));
    }
}
```

_We call it [Composition over Inheritance][wiki:compositioninheritance]._

# Installation

```bash
composer require ro0nl/http-responder
```

## Enable the Symfony Bundle

```php
return [
    // ...
    ro0NL\HttpResponder\Bundle\HttpResponderBundle::class => ['all' => true],
];
```

# Creating a Providing Responder

```php
use ro0NL\HttpResponder\ProvidingResponder;
use ro0NL\HttpResponder\Respond\Respond;
use Symfony\Component\HttpFoundation\Response;

class MyRespond implements Respond
{
}

class MyProvidingResponder extends ProvidingResponder
{
    protected function getProviders(): iterable
    {
        yield MyRespond::class => function (MyRespond $respond): Response {
            return new Response('hello world');
        };
    }
}
```

Any type of `ProvidingResponder` service is automatically tagged with `http_responder` to be made available in the main
responder.

In case your responder implements the `Responder` interface but serves as a provider it should be tagged manually.

# Creating a Decorating Responder

To add behaviors to the main responder use a decorator.

```php
use ro0NL\HttpResponder\Responder;
use ro0NL\HttpResponder\Respond\Respond;
use Symfony\Component\HttpFoundation\Response;

class MyDecoratingResponder implements Responder
{
    private $responder;

    public function __construct(Responder $responder)
    {
        $this->responder = $responder;
    }

    public function respond(Respond $respond): Response
    {
        // provide a response and ignore/override default behaviors
        if ($respond instanceof SpecialRespond) {
            return new Response('special');
        }

        // provide the initial response with default behaviors
        $response = $this->responder->respond($respond);

        // apply some generic behavior

        if ($respond instanceof MyRespond) {
            // apply some specific behavior
        }

        return $response;
    }
}
```

The bundle's main service identifier is `http_responder` and is aliased to its corresponding interface.

# Comparison Table

&nbsp; | [`AbstractController`][sf:controller] | `Responder`
--- | --- | ---
`get()` | ✔️ | ❌ (use DI)
`has()` | ✔️ | ❌ (use DI)
`generateUrl()` | ✔️ | ✔️
`forward()` | ✔️ | ✔️ (todo)
`redirect()` | ✔️ | ✔️
`redirectToRoute()` | ✔️ | ✔️
`json()` | ✔️ | ✔️ (todo `Serializer` support)
`file()` | ✔️ | ✔️
`addFlash()` | ✔️ | ✔️
`isGranted()` | ✔️ | ❌ (use `Security` service)
`denyAccessUnlessGranted()` | ✔️ | ❌ (use code / `Security` firewall)
`renderView()` | ✔️ | ❌ (use `Twig` service)
`render()` | ✔️ | ✔️
`stream()` | ✔️ | ✔️
`createNotFoundException()` | ✔️ | ❌ (use `throw`)
`createAccessDeniedException()` | ✔️ | ❌ (use `throw`)
`createForm()` | ✔️ | ❌ (use `Form` service)
`createFormBuilder()` | ✔️ | ❌ (use `Form` service)
`getDoctrine()` | ✔️ | ❌ (use `Doctrine` service)
`getUser()` | ✔️ | ❌ (use `Security` service)
`isCsrfTokenValid()` | ✔️ | ❌ (use `Csrf` service)
`dispatchMessage()` | ✔️ | ❌ (use `Messenger` service)
`addLink()` | ✔️ | ✔️

# Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md)

[master:travis]: https://travis-ci.org/ro0NL/symfony-http-responder
[master:travis:img]: https://img.shields.io/travis/ro0NL/symfony-http-responder/master.svg?style=flat-square
[packagist]: https://packagist.org/packages/ro0NL/http-responder
[packagist:img]: https://img.shields.io/packagist/v/ro0NL/http-responder.svg?style=flat-square
[wiki:adr]: https://en.wikipedia.org/wiki/Action%E2%80%93domain%E2%80%93responder
[wiki:compositioninheritance]: https://en.wikipedia.org/wiki/Composition_over_inheritance
[sf:controller]: https://github.com/symfony/symfony/blob/master/src/Symfony/Bundle/FrameworkBundle/Controller/AbstractController.php
