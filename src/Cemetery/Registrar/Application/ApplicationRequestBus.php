<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

use Cemetery\Registrar\Domain\Model\Exception as DomainException;
use Cemetery\Registrar\Infrastructure\DependencyInjection\ApplicationServiceLocator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ApplicationRequestBus
{
    /**
     * @param ApplicationServiceLocator $appServiceLocator
     */
    public function __construct(
        private readonly ApplicationServiceLocator $appServiceLocator,
    ) {}

    /**
     * @param $request
     *
     * @return mixed|void
     *
     * @throws NotFoundExceptionInterface  when  No entry was found for application identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     */
    public function execute($request)
    {
        $requestClassName = \get_class($request);
        $appRequestName   = \substr(strrchr($requestClassName, '\\'), 1);
        $appServiceId     = 'app.service.'.\strtolower(\str_replace('Request', '', $appRequestName));

        /** @var ApplicationService $appService */
        $appService = $this->appServiceLocator->get($appServiceId);
        try {
            $response = $appService->execute($request);
        } catch (DomainException $e) {

        }

        return $response;
    }
}
