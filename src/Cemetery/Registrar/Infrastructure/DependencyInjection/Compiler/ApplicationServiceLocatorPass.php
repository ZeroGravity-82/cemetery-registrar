<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\DependencyInjection\Compiler;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Infrastructure\DependencyInjection\ApplicationServiceLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to add application services to the application service locator.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ApplicationServiceLocatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $appServices = [];
        foreach($container->getServiceIds() as $id) {
            if (\is_subclass_of($id, ApplicationService::class)) {
                $appServiceName             = \substr(strrchr($id, '\\'), 1);
                $appServiceId               = 'app.service.'.\strtolower(\str_replace('Service', '', $appServiceName));
                $appServices[$appServiceId] = new Reference($id);
            }
        }
        $appServiceLocator = $container->getDefinition(ApplicationServiceLocator::class);
        $appServiceLocator->setArguments([$appServices]);
    }
}
