<?php

declare(strict_types=1);

namespace App\RoadrunnerBundle\Reboot;

class AlwaysRebootStrategy implements KernelRebootStrategyInterface
{
    public function shouldReboot(): bool
    {
        return true;
    }

    public function clear(): void
    {
    }
}
