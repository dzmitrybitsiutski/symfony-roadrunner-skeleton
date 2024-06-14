<?php

declare(strict_types=1);

namespace App\RoadrunnerBundle\Integration\Sentry;

use App\RoadrunnerBundle\Event\WorkerExceptionEvent;
use App\RoadrunnerBundle\Event\WorkerStopEvent;
use GuzzleHttp\Promise\PromiseInterface;
use Sentry\State\HubInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SentryListener implements EventSubscriberInterface
{
    public function __construct(private HubInterface $hub)
    {
    }

    public function onWorkerStop(WorkerStopEvent $event): void
    {
        $result = $this->hub->getClient()?->flush();
        if (class_exists(PromiseInterface::class) && $result instanceof PromiseInterface) {
            $result->wait(false);
        }
    }

    public function onWorkerException(WorkerExceptionEvent $event): void
    {
        $this->hub->captureException($event->getException());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerStopEvent::class => 'onWorkerStop',
            WorkerExceptionEvent::class => 'onWorkerException',
        ];
    }
}
