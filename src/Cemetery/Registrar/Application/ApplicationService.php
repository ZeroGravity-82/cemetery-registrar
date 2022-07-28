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
     * Returns the name of the supported request class.
     *
     * @return string
     */
    abstract public function supportedRequestClassName(): string;

    /**
     * @param $request
     *
     * @return mixed|void
     *
     * @throws DomainException when there was any issue within the domain
     * @throws \Throwable      when any error occurred while processing the request
     */
    abstract public function execute($request);

    /**
     * @param $request
     *
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
