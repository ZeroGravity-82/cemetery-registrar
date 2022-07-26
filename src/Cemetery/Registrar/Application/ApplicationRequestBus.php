<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

use Cemetery\Registrar\Infrastructure\DependencyInjection\ApplicationServiceLocator;

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
     */
    public function execute($request)
    {
        $requestClassName = \get_class($request);
        $appRequestName   = \substr(strrchr($requestClassName, '\\'), 1);
        $appServiceId     = 'app.service.'.\strtolower(\str_replace('Request', '', $appRequestName));

        /** @var ApplicationService $appService */
        $appService = $this->appServiceLocator->get($appServiceId);

        return $appService->execute($request);
    }
}
