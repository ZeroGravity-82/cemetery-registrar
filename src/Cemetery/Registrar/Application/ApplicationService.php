<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class ApplicationService
{
    /**
     * @param $request
     *
     * @return mixed|void
     */
    abstract public function execute($request);

    /**
     * @param object $request
     * @param string $supportedClass
     *
     * @throws \InvalidArgumentException when the request object is not an instance of the supported class
     */
    protected function assertInstanceOf(object $request, string $supportedClass): void
    {
        if (!$request instanceof $supportedClass) {
            throw new \InvalidArgumentException(\sprintf(
                'The only argument of the %s::execute method must be instance of %s class.',
                \get_class($this),
                $supportedClass,
            ));
        }
    }
}
