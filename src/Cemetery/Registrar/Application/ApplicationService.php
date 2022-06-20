<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

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
                'The only argument of the "%s::execute" method must be instance of "%s" class, "%s" given.',
                \get_class($this),
                $supportedRequestClassName,
                \is_object($request) ? \get_class($request) : \get_debug_type($request),
            ));
        }
    }
}
