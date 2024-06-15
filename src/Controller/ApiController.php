<?php

namespace App\Controller;

use App\Workflow\SimpleActivity\GreetingWorkflowInterface;
use Carbon\CarbonInterval;
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
            GreetingWorkflowInterface::class,
            WorkflowOptions::new()->withWorkflowExecutionTimeout(CarbonInterval::minute())
        );

        $this->logger->debug("Starting <comment>GreetingWorkflow</comment>... ");

        // Start a workflow execution. Usually this is done from another program.
        // Uses task queue from the GreetingWorkflow @WorkflowMethod annotation.
        $run = $this->workflowClient->start($workflow, 'Antony');

        return $this->json([
            'message' => 'Welcome to the API!',
        ]);
    }
}