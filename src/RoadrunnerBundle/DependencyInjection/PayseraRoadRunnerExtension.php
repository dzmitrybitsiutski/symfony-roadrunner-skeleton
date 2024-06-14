<?php

declare(strict_types=1);

namespace App\RoadrunnerBundle\DependencyInjection;

use App\RoadrunnerBundle\Cache\KvCacheAdapter;
use App\RoadrunnerBundle\Event\WorkerStartEvent;
use App\RoadrunnerBundle\EventListener\DeclareMetricsListener;
use App\RoadrunnerBundle\Integration\Blackfire\BlackfireMiddleware;
use App\RoadrunnerBundle\Integration\Doctrine\DoctrineORMMiddleware;
use App\RoadrunnerBundle\Integration\Sentry\SentryListener;
use App\RoadrunnerBundle\Integration\Sentry\SentryMiddleware;
use App\RoadrunnerBundle\Integration\Sentry\SentryTracingRequestListenerDecorator;
use App\RoadrunnerBundle\Integration\Symfony\ConfigureVarDumperListener;
use App\RoadrunnerBundle\Reboot\AlwaysRebootStrategy;
use App\RoadrunnerBundle\Reboot\ChainRebootStrategy;
use App\RoadrunnerBundle\Reboot\KernelRebootStrategyInterface;
use App\RoadrunnerBundle\Reboot\MaxJobsRebootStrategy;
use App\RoadrunnerBundle\Reboot\OnExceptionRebootStrategy;
use App\RoadrunnerBundle\Worker\GrpcWorker;
use App\RoadrunnerBundle\Worker\HttpWorker;
use App\RoadrunnerBundle\Worker\Job\JobWorker;
use App\RoadrunnerBundle\Worker\WorkerRegistry;
use App\RoadrunnerBundle\Worker\WorkerRegistryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Sentry\SentryBundle\EventListener\TracingRequestListener;
use Sentry\State\HubInterface;
use Spiral\Goridge\RPC\RPC;
use Spiral\Goridge\RPC\RPCInterface;
use Spiral\RoadRunner\Environment\Mode;
use Spiral\RoadRunner\GRPC\ServiceInterface;
use Spiral\RoadRunner\KeyValue\Factory;
use Spiral\RoadRunner\Metrics\Collector;
use Spiral\RoadRunner\Metrics\MetricsInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PayseraRoadRunnerExtension extends Extension
{
    public const MONOLOG_CHANNEL = 'roadrunner';

    /**
     * @param array<string, mixed> $configs
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

       /* $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');*/

        /*$loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');*/

        if ($container->getParameter('kernel.debug')) {
            $this->loadDebug($container);
        }

       /* $container->register(WorkerRegistryInterface::class, WorkerRegistry::class)
            ->setPublic(true)
            ->addMethodCall('registerWorker', [
                Mode::MODE_HTTP,
                new Reference(HttpWorker::class),
            ])
            ->addMethodCall('registerWorker', [
                Mode::MODE_GRPC,
                new Reference(GrpcWorker::class),
            ])
            ->addMethodCall('registerWorker', [
                Mode::MODE_JOBS,
                new Reference(JobWorker::class),
            ]);*/

        $strategies = $config['kernel_reboot']['strategy'];
        $strategyServices = [];

        foreach ($strategies as $strategy) {
            if ($strategy === Configuration::KERNEL_REBOOT_STRATEGY_ALWAYS) {
                $strategyService = (new Definition(AlwaysRebootStrategy::class))
                    ->setAutoconfigured(true);
            } elseif ($strategy === Configuration::KERNEL_REBOOT_STRATEGY_ON_EXCEPTION) {
                $strategyService = (new Definition(OnExceptionRebootStrategy::class))
                    ->addArgument(new Reference(LoggerInterface::class))
                    ->addArgument($config['kernel_reboot']['allowed_exceptions'])
                    ->setAutoconfigured(true)
                    ->addTag('monolog.logger', ['channel' => self::MONOLOG_CHANNEL]);
            } elseif ($strategy === Configuration::KERNEL_REBOOT_STRATEGY_MAX_JOBS) {
                $strategyService = (new Definition(MaxJobsRebootStrategy::class))
                    ->addArgument($config['kernel_reboot']['max_jobs'])
                    ->addArgument($config['kernel_reboot']['max_jobs_dispersion'])
                    ->setAutoconfigured(true);
            } else {
                $strategyService = new Reference($strategy);
            }

            $strategyServices[] = $strategyService;
        }

        if (\count($strategyServices) > 1) {
            $container->register(KernelRebootStrategyInterface::class, ChainRebootStrategy::class)
                ->setArguments([$strategyServices]);
        } else {
            $strategy = $strategyServices[0];

            if ($strategy instanceof Reference) {
                $container->setAlias(KernelRebootStrategyInterface::class, (string) $strategy);
            } else {
                $container->setDefinition(KernelRebootStrategyInterface::class, $strategy);
            }
        }

        $container->setParameter('paysera_road_runner.middlewares', $config['middlewares']);

        $this->loadIntegrations($container, $config);

        if ($config['metrics']['enabled']) {
            $this->configureMetrics($config, $container);
        }

        if (!empty($config['kv']['storages'])) {
            $this->configureKv($config, $container);
        }

        if (interface_exists(ServiceInterface::class)) {
            $container->registerForAutoconfiguration(ServiceInterface::class)
                ->addTag('paysera.roadrunner.grpc_service');
        }
    }

    private function loadDebug(ContainerBuilder $container): void
    {
        $container->register(ConfigureVarDumperListener::class, ConfigureVarDumperListener::class)
            ->addTag('kernel.event_listener', ['event' => WorkerStartEvent::class])
            ->addArgument(new Reference('data_collector.dump'))
            ->addArgument(new Reference('var_dumper.cloner'))
            ->addArgument('%env(default::RR_MODE)%');
    }

    private function loadIntegrations(ContainerBuilder $container, array $config): void
    {
        $beforeMiddlewares = [];
        $lastMiddlewares = [];

        if (!$config['default_integrations']) {
            $container->setParameter('paysera_road_runner.middlewares.default', ['before' => $beforeMiddlewares, 'after' => $lastMiddlewares]);

            return;
        }

        /** @var array */
        $bundles = $container->getParameter('kernel.bundles');

        if (class_exists(\BlackfireProbe::class)) {
            $container->register(BlackfireMiddleware::class);
            $beforeMiddlewares[] = BlackfireMiddleware::class;
        }

        if (isset($bundles['SentryBundle'])) {
            $container
                ->register(SentryMiddleware::class)
                ->addArgument(new Reference(HubInterface::class));

            $container
                ->register(SentryListener::class)
                ->addArgument(new Reference(HubInterface::class))
                ->setAutoconfigured(true);

            $container
                ->register(SentryTracingRequestListenerDecorator::class)
                ->setDecoratedService(TracingRequestListener::class, null, 0, ContainerInterface::IGNORE_ON_INVALID_REFERENCE)
                ->setArguments([
                    new Reference(SentryTracingRequestListenerDecorator::class.'.inner'),
                    new Reference(HubInterface::class),
                ]);

            $beforeMiddlewares[] = SentryMiddleware::class;
        }


        $container->setParameter('paysera_road_runner.middlewares.default', ['before' => $beforeMiddlewares, 'after' => $lastMiddlewares]);
    }

    private function configureMetrics(array $config, ContainerBuilder $container): void
    {
        if (!interface_exists(MetricsInterface::class)) {
            throw new LogicException('RoadRunner Metrics support cannot be enabled as spiral/roadrunner-metrics is not installed. Try running "composer require spiral/roadrunner-metrics".');
        }

        $listenerDef = $container->register(DeclareMetricsListener::class)
            ->setAutoconfigured(true)
            ->addArgument(new Reference(MetricsInterface::class));

        foreach ($config['metrics']['collect'] as $name => $metric) {
            $def = new Definition(Collector::class);
            $def->setFactory([Collector::class, $metric['type']]);

            $id = "paysera_road_runner.metrics.internal.collector.$name";
            $container->setDefinition($id, $def);

            $listenerDef->addMethodCall('addCollector', [$name, $metric]);
        }
    }

    private function configureKv(array $config, ContainerBuilder $container): void
    {
        if (!class_exists(Factory::class)) {
            throw new LogicException('RoadRunner KV support cannot be enabled as spiral/roadrunner-kv is not installed. Try running "composer require spiral/roadrunner-kv".');
        }

        if (!class_exists(RPC::class)) {
            throw new LogicException('RoadRunner KV support cannot be enabled as spiral/goridge is not installed. Try running "composer require spiral/goridge".');
        }

        if (!interface_exists(AdapterInterface::class)) {
            throw new LogicException('RoadRunner KV support cannot be enabled as symfony/cache is not installed. Try running "composer require symfony/cache".');
        }

        $storages = $config['kv']['storages'];

        foreach ($storages as $storage) {
            $container->register('cache.adapter.roadrunner.kv_'.$storage, KvCacheAdapter::class)
                ->setFactory([KvCacheAdapter::class, 'createConnection'])
                ->setArguments(['', [ // Symfony overrides the first argument with the DSN, so we pass an empty string
                    'rpc' => $container->getDefinition(RPCInterface::class),
                    'storage' => $storage,
                ]]);
        }
    }
}
