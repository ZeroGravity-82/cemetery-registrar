<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class ApplicationService
{
    /**
     * @throws \InvalidArgumentException when the request is not an instance of the supported class
     */
    abstract public function validate(ApplicationRequest $request): Notification;

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    abstract public function execute(ApplicationRequest $request): ApplicationSuccessResponse;

    abstract protected function supportedRequestClassName(): string;

    /**
     * @throws \InvalidArgumentException when the request is not an instance of the supported class
     */
    protected function assertSupportedRequestClass($request): void
    {
        $supportedRequestClassName = $this->supportedRequestClassName();
        if (!$request instanceof $supportedRequestClassName) {
            throw new \InvalidArgumentException(\sprintf(
                'Единственным аргументом метода "%s::execute" должен быть экземпляр класса "%s", "%s" передан.',
                \get_class($this),
                $supportedRequestClassName,
                \is_object($request) ? \get_class($request) : \get_debug_type($request),
            ));
        }
    }
}
