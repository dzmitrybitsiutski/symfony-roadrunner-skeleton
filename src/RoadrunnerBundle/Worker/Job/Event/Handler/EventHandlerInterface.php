<?php

declare(strict_types=1);

namespace App\RoadrunnerBundle\Worker\Job\Event\Handler;

interface EventHandlerInterface
{
    public function handle(object $event);
}
