<?php

declare(strict_types=1);

namespace App\RoadrunnerBundle\Worker;

interface WorkerRegistryInterface
{
    public function registerWorker(string $mode, WorkerInterface $worker): void;

    public function getWorker(string $mode): ?WorkerInterface;
}
