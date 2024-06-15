<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Workflow\SimpleActivity;

use Paysera\RoadRunnerBundle\Worker\Temporal\BaseActivityInterface;

// @@@SNIPSTART php-hello-activity
class GreetingActivity implements GreetingActivityInterface, BaseActivityInterface
{
    public function composeGreeting(string $greeting, string $name): string
    {
        return $greeting . ' ' . $name;
    }
}
// @@@SNIPEND
