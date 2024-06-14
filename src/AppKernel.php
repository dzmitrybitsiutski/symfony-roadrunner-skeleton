<?php

declare(strict_types=1);

namespace App;

use Doctrine;
use Nelmio;
use App;
use Symfony;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Symfony\Bundle\MakerBundle\MakerBundle(),
            new App\RoadrunnerBundle\PayseraRoadRunnerBundle(),
        ];
    }

    public function boot(): void
    {
        mb_internal_encoding('UTF-8');
        parent::boot();
    }
}
