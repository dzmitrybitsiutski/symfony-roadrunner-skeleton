<?php

declare(strict_types=1);

namespace App\RoadrunnerBundle\Worker;

use App\RoadrunnerBundle\Grpc\GrpcServiceProvider;
use Psr\Log\LoggerInterface;
use Spiral\RoadRunner\GRPC\Server;
use Spiral\RoadRunner\Worker as RoadRunnerWorker;

use function sprintf;

/**
 * @internal
 */
final class GrpcWorker implements WorkerInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private RoadRunnerWorker $roadRunnerWorker,
        private GrpcServiceProvider $grpcServiceProvider,
        private Server $server
    ) {
    }

    public function serve(): void
    {
        foreach ($this->grpcServiceProvider->getRegisteredServices() as $interface => $service) {
            $this->logger->debug(
                sprintf(
                    'Registering GRPC service for \'%s\' from \'%s\'',
                    $interface,
                    \get_class($service),
                ),
            );

            $this->server->registerService($interface, $service);
        }

        $this->server->serve($this->roadRunnerWorker);
    }
}
