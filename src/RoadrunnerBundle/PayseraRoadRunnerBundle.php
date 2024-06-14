<?php

declare(strict_types=1);

namespace App\RoadrunnerBundle;

use App\RoadrunnerBundle\DependencyInjection\CompilerPass\GrpcServiceCompilerPass;
use App\RoadrunnerBundle\DependencyInjection\CompilerPass\MiddlewareCompilerPass;
use App\RoadrunnerBundle\DependencyInjection\CompilerPass\RemoveConfigureVarDumperListenerPass;
use Spiral\RoadRunner\GRPC\ServiceInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class PayseraRoadRunnerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RemoveConfigureVarDumperListenerPass());
        $container->addCompilerPass(new MiddlewareCompilerPass());
        if (interface_exists(ServiceInterface::class)) {
            $container->addCompilerPass(new GrpcServiceCompilerPass());
        }
    }
}
