<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\RoadrunnerBundle\DependencyInjection\PayseraRoadRunnerExtension;
use App\RoadrunnerBundle\Grpc\GrpcServiceProvider;
use App\RoadrunnerBundle\Helpers\RPCFactory;
use App\RoadrunnerBundle\Http\KernelHandler;
use App\RoadrunnerBundle\Http\MiddlewareStack;
use App\RoadrunnerBundle\Http\RequestHandlerInterface;
use App\RoadrunnerBundle\Reboot\KernelRebootStrategyInterface;
use App\RoadrunnerBundle\RoadRunnerBridge\HttpFoundationWorker;
use App\RoadrunnerBundle\RoadRunnerBridge\HttpFoundationWorkerInterface;
use App\RoadrunnerBundle\Worker\GrpcWorker as InternalGrpcWorker;
use App\RoadrunnerBundle\Worker\HttpDependencies;
use App\RoadrunnerBundle\Worker\HttpWorker as InternalHttpWorker;
use App\RoadrunnerBundle\Worker\Job\Event\Handler\EventHandler;
use App\RoadrunnerBundle\Worker\Job\Event\Handler\EventHandlerInterface;
use App\RoadrunnerBundle\Worker\Job\Event\Serializer\Serializer;
use App\RoadrunnerBundle\Worker\Job\JobWorker as InternalJobWorker;
use App\RoadrunnerBundle\Worker\WorkerRegistry;
use App\RoadrunnerBundle\Worker\WorkerRegistryInterface;
use Psr\Log\LoggerInterface;
use Sentry\State\Hub;
use Sentry\State\HubInterface;
use Spiral\Goridge\RPC\RPCInterface;
use Spiral\RoadRunner\Environment;
use Spiral\RoadRunner\EnvironmentInterface;
use Spiral\RoadRunner\GRPC\Invoker as GrpcInvoker;
use Spiral\RoadRunner\GRPC\Server as GrpcServer;
use Spiral\RoadRunner\GRPC\ServiceInterface as GrpcServiceInterface;
use Spiral\RoadRunner\Http\HttpWorker;
use Spiral\RoadRunner\Http\HttpWorkerInterface;
use Spiral\RoadRunner\Metrics\Metrics;
use Spiral\RoadRunner\Metrics\MetricsInterface;
use Spiral\RoadRunner\Worker;
use Spiral\RoadRunner\Worker as RoadRunnerWorker;
use Spiral\RoadRunner\WorkerInterface as RoadRunnerWorkerInterface;
use Spiral\RoadRunner\Jobs\ConsumerInterface;
use Spiral\RoadRunner\Jobs\Consumer;
use Spiral\RoadRunner\Jobs\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

return static function (ContainerConfigurator $container) {
    $container->parameters()
        ->set('paysera_road_runner.intercept_side_effect', true);

    $services = $container->services();

    // RoadRuner services
    $services->set(EnvironmentInterface::class)
        ->factory([Environment::class, 'fromGlobals']);

    $services->set(RoadRunnerWorkerInterface::class, RoadRunnerWorker::class)
        ->factory([RoadRunnerWorker::class, 'createFromEnvironment'])
        ->args([service(EnvironmentInterface::class), '%paysera_road_runner.intercept_side_effect%']);

    $services->set(HttpWorkerInterface::class, HttpWorker::class)
        ->args([service(RoadRunnerWorkerInterface::class)]);

    $services->set(RPCInterface::class)
        ->factory([RPCFactory::class, 'fromEnvironment'])
        ->args([service(EnvironmentInterface::class)]);

    $services->set(MetricsInterface::class, Metrics::class)
        ->args([service(RPCInterface::class)]);

    // Bundle services
    $services->set(HttpFoundationWorkerInterface::class, HttpFoundationWorker::class)
        ->args([service(HttpWorkerInterface::class)]);

    /*$services->set(WorkerRegistryInterface::class, WorkerRegistry::class)
        ->public();*/

    $services->set(InternalHttpWorker::class)
        ->public()
        ->tag('monolog.logger', ['channel' => PayseraRoadRunnerExtension::MONOLOG_CHANNEL])
        ->args([
            service('kernel'),
            service(LoggerInterface::class),
            service(HttpFoundationWorkerInterface::class),
        ]);

    /*$services
        ->get(WorkerRegistryInterface::class)
        ->public()
        ->call('registerWorker', [
            Environment\Mode::MODE_HTTP,
            service(InternalHttpWorker::class),
        ]);*/

    $services->set(HttpDependencies::class)
        ->public() // Manually retrieved on the DIC in the Worker if the kernel has been rebooted
        ->args([
            service(MiddlewareStack::class),
            service(KernelRebootStrategyInterface::class),
            service(EventDispatcherInterface::class),
        ]);

    $services->set(KernelHandler::class)
        ->args([
            service('kernel'),
        ]);

    $services->set(MiddlewareStack::class)
        ->args([service(KernelHandler::class)]);

    $services->alias(RequestHandlerInterface::class, MiddlewareStack::class);

    if (interface_exists(GrpcServiceInterface::class)) {
        $services->set(GrpcServiceProvider::class);
        $services->set(GrpcInvoker::class);

        $services->set(GrpcServer::class)
            ->args([
                service(GrpcInvoker::class),
            ]);

        $services->set(InternalGrpcWorker::class)
            ->public() // Manually retrieved on the DIC in the Worker if the kernel has been rebooted
            ->tag('monolog.logger', ['channel' => PayseraRoadRunnerExtension::MONOLOG_CHANNEL])
            ->args([
                service(LoggerInterface::class),
                service(RoadRunnerWorkerInterface::class),
                service(GrpcServiceProvider::class),
                service(GrpcServer::class),
            ]);

        /*$services
            ->get(WorkerRegistryInterface::class)
            ->public()
            ->call('registerWorker', [
                Environment\Mode::MODE_GRPC,
                service(InternalGrpcWorker::class),
            ]);*/
    }

    // job roadrunner worker
    $services->set(EventHandlerInterface::class, EventHandler::class)
        ->public()
        ->args([
            service(LoggerInterface::class),
        ]);

    $services->set(ConsumerInterface::class, Consumer::class)
        ->public()
        ->args([
            service(RoadRunnerWorkerInterface::class),
        ]);

    $services->set(InternalJobWorker::class)
        ->public() // Manually retrieved on the DIC in the Worker if the kernel has been rebooted
        ->tag('monolog.logger', ['channel' => PayseraRoadRunnerExtension::MONOLOG_CHANNEL])
        ->args([
            service('kernel'),
            service(LoggerInterface::class),
            service(ConsumerInterface::class),
            service(EventHandlerInterface::class),
        ]);

    /*$services
        ->get(WorkerRegistryInterface::class)
        ->call('registerWorker', [
            Environment\Mode::MODE_JOBS,
            service(InternalJobWorker::class),
        ]);*/

    $services->set(HubInterface::class, Hub::class)
        ->public();
};
