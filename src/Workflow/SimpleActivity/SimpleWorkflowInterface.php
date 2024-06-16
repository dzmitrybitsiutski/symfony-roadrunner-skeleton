<?php

declare(strict_types=1);

namespace App\Workflow\SimpleActivity;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface SimpleWorkflowInterface
{
    /**
     * @param string $name
     * @return string
     */
    #[WorkflowMethod(name: "SimpleActivity.simple")]
    public function simple(
        string $name
    );
}
