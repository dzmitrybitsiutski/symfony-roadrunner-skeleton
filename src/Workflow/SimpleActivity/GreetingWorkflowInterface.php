<?php

declare(strict_types=1);

namespace App\Workflow\SimpleActivity;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface GreetingWorkflowInterface
{
    /**
     * @param string $name
     * @return string
     */
    #[WorkflowMethod(name: "SimpleActivity.greet")]
    public function greet(
        string $name
    );
}
