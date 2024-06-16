<?php

declare(strict_types=1);

namespace App\Workflow\SimpleActivity;

use Paysera\RoadRunnerBundle\Worker\Temporal\BaseActivityInterface;

class SimpleActivity implements SimpleActivityInterface, BaseActivityInterface
{
    public function compose(string $greeting, string $name): string
    {
        return 'Running:' . $greeting . ' ' . $name;
    }

    public function compile(string $greeting, string $name): string
    {
        return 'Compile:' . $greeting . ' ' . $name;
    }

    public function decline(string $greeting, string $name): string
    {
        return 'Decline:' . $greeting . ' ' . $name;
    }
}
