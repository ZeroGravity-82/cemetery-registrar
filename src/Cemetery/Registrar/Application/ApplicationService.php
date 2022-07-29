<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

use Cemetery\Registrar\Domain\Model\Exception as DomainException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class ApplicationService
{
    /**
     * Executes the application request.
     *
     * @param ApplicationRequest $request
     *
     * @return ApplicationResponseSuccess
     *
     * @throws DomainException when there was any issue within the domain
     * @throws \Throwable      when any error occurred while processing the request
     */
    abstract public function execute(ApplicationRequest $request): ApplicationResponseSuccess;

    /**
     * Checks whether the application request is of a type supported by the service.
     *
     * @param $request
     *
     * @throws \InvalidArgumentException when the request is not an instance of the supported class
     */
    public function assertSupportedRequestClass($request): void
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

    /**
     * Returns the name of the supported request class.
     *
     * @return string
     */
    abstract protected function supportedRequestClassName(): string;
}
