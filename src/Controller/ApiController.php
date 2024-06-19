<?php

namespace App\Controller;

use App\Workflow\SimpleActivity\SimpleWorkflowInterface;
use Carbon\CarbonInterval;
use Paysera\RoadRunnerBundle\Environment\App\AppEnvironment;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Client\WorkflowOptions;

class ApiController extends AbstractController
{
    public function __construct(
        public WorkflowClientInterface $workflowClient,
        public LoggerInterface $logger,
    ) {

    }
    #[Route('/api', name: 'app_api')]
    public function index(): JsonResponse
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            SimpleWorkflowInterface::class,
            WorkflowOptions::new()
                ->withTaskQueue(AppEnvironment::fromEnv()->getService())
                ->withWorkflowExecutionTimeout(CarbonInterval::minute())
        );

        $this->logger->debug("Starting SimpleWorkflow... ");

        // Start a workflow execution. Usually this is done from another program.
        // Uses task queue from the SimpleWorkflow @WorkflowMethod annotation.
        $this->workflowClient->start($workflow, 'Example');

        return $this->json([
            'message' => 'Welcome to the API!',
        ]);
    }
}