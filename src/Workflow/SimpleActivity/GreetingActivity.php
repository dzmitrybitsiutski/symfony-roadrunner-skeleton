<?php

declare(strict_types=1);

namespace App\Workflow\SimpleActivity;

use Paysera\RoadRunnerBundle\Worker\Temporal\BaseActivityInterface;

class GreetingActivity implements GreetingActivityInterface, BaseActivityInterface
{
    public function composeGreeting(string $greeting, string $name): string
    {
        return 'Running:' . $greeting . ' ' . $name;
    }

    public function compileGreeting(string $greeting, string $name): string
    {
        return 'Compile:' . $greeting . ' ' . $name;
    }

    public function declineGreeting(string $greeting, string $name): string
    {
        return 'Decline:' . $greeting . ' ' . $name;
    }
}
