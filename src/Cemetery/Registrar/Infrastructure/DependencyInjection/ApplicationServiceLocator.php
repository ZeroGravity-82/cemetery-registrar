<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\DependencyInjection;

use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Small service locator that only knows about application services. It is initialized in the
 * ApplicationServiceLocatorPass.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ApplicationServiceLocator extends ServiceLocator
{
}
