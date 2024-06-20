<?php

declare(strict_types=1);

namespace App\Workflow\SimpleActivity;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Workflow;
use Paysera\Bundle\RoadRunnerBundle\Worker\Temporal\BaseWorkflowInterface;

class SimpleWorkflow implements SimpleWorkflowInterface, BaseWorkflowInterface
{
    private $greetingActivity;

    public function __construct()
    {
        /**
         * Activity stub implements activity interface and proxies calls to it to Temporal activity
         * invocations. Because activities are reentrant, only a single stub can be used for multiple
         * activity invocations.
         */
        $this->greetingActivity = Workflow::newActivityStub(
            SimpleActivityInterface::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(CarbonInterval::seconds(40))
                ->withRetryOptions(
                    RetryOptions::new()
                        ->withInitialInterval(5)
                        ->withBackoffCoefficient(2.0)
                        ->withMaximumAttempts(3),
                ),
        );
    }

    public function simple(string $name): \Generator
    {
        // This is a blocking call that returns only after the activity has completed.
        $saga = new Workflow\Saga();
        $saga->setParallelCompensation(false);

        try {
            $saga->addCompensation(fn (): \Generator => yield $this->greetingActivity->decline('Hello', $name));

            yield Workflow::timer(CarbonInterval::seconds(10));

            yield $this->greetingActivity->compose('Hello', $name);

            yield $this->greetingActivity->compile('Hello', $name);
        } catch (\Throwable $throwable) {
            yield $saga->compensate();

            throw $throwable;
        }
    }
}
