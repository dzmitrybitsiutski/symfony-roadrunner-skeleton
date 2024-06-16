<?php

declare(strict_types=1);

namespace App\Workflow\SimpleActivity;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface(prefix: 'SimpleActivity.')]
interface SimpleActivityInterface
{
    #[ActivityMethod(name: "Compose")]
    public function compose(
        string $greeting,
        string $name
    ): string;

    #[ActivityMethod(name: "Compile")]
    public function compile(
        string $greeting,
        string $name
    ): string;

    #[ActivityMethod(name: "Decline")]
    public function decline(
        string $greeting,
        string $name
    ): string;
}
