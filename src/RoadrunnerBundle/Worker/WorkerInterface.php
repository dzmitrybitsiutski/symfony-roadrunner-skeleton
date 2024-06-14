<?php

declare(strict_types=1);

namespace App\RoadrunnerBundle\Worker;

interface WorkerInterface
{
    public function serve(): void;
}
