<?php

namespace Cemetery;

use Cemetery\Registrar\Infrastructure\DependencyInjection\Compiler\ApplicationServiceLocatorPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ApplicationServiceLocatorPass());
    }
}
