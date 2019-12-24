<?php

declare(strict_types=1);

namespace ro0NL\HttpResponder\Bundle\EventListener;

use ro0NL\HttpResponder\Respond\Respond;
use ro0NL\HttpResponder\Responder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @internal
 *
 * @psalm-suppress UnusedClass
 */
final class RespondListener implements EventSubscriberInterface
{
    /** @var Responder */
    private $responder;

    public function __construct(Responder $responder)
    {
        $this->responder = $responder;
    }

    /**
     * @param GetResponseForControllerResultEvent|ViewEvent $event
     *
     * @psalm-suppress UndefinedDocblockClass
     * @psalm-suppress MismatchingDocblockParamType
     */
    public function onKernelView(KernelEvent $event): void
    {
        $controllerResult = $event->getControllerResult();
        if (!$controllerResult instanceof Respond) {
            return;
        }

        $event->setResponse($this->responder->respond($controllerResult));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['onKernelView'],
            ],
        ];
    }
}
