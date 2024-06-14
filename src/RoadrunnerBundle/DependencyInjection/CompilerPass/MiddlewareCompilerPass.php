<?php

declare(strict_types=1);

namespace App\RoadrunnerBundle\DependencyInjection\CompilerPass;

use App\RoadrunnerBundle\Http\MiddlewareInterface;
use App\RoadrunnerBundle\Http\MiddlewareStack;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Reference;

class MiddlewareCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(MiddlewareStack::class)) {
            return;
        }

        $stack = $container->getDefinition(MiddlewareStack::class);

        /** @var string[] */
        $middlewares = $container->getParameter('paysera_road_runner.middlewares');
        /** @var array{before: string[], after: string[]} */
        $defaultMiddlewares = $container->getParameter('paysera_road_runner.middlewares.default');

        $middlewaresToRemove = [];

        $beforeMiddlewares = array_diff($defaultMiddlewares['before'], $middlewaresToRemove);
        $afterMiddlewares = array_diff($defaultMiddlewares['after'], $middlewaresToRemove);

        foreach (array_merge($beforeMiddlewares, $middlewares, $afterMiddlewares) as $m) {
            if (!$container->has($m)) {
                throw new LogicException("No service found for middleware '$m'.");
            }

            $definition = $container->findDefinition($m);
            $class = $definition->getClass();

            if (null === $class) {
                throw new InvalidArgumentException("Missing class definition for service '$m'.");
            }

            if (!is_a($class, MiddlewareInterface::class, true) && !is_a($class, MiddlewareInterface::class, true)) {
                throw new InvalidArgumentException(sprintf("Service '%s' should implements '%s'.", $m, MiddlewareInterface::class));
            }

            $stack->addMethodCall('pipe', [new Reference($m)]);
        }
    }
}
