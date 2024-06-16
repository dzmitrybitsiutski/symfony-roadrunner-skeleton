<?php

declare(strict_types=1);

namespace App\Workflow\SimpleActivity;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface(prefix: 'SimpleActivity.')]
interface GreetingActivityInterface
{
    #[ActivityMethod(name: "ComposeGreeting")]
    public function composeGreeting(
        string $greeting,
        string $name
    ): string;

    #[ActivityMethod(name: "CompileGreeting")]
    public function compileGreeting(
        string $greeting,
        string $name
    ): string;

    #[ActivityMethod(name: "DeclineGreeting")]
    public function declineGreeting(
        string $greeting,
        string $name
    ): string;
}
