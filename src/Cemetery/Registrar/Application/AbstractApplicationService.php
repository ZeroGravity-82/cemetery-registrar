<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractApplicationService
{
    public function __construct(
        private AbstractApplicationRequestValidator $requestValidator,
    ) {}

    /**
     * @throws \InvalidArgumentException when the request is not an instance of the supported class
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        $this->assertSupportedRequestClass($request);

        return $this->requestValidator->validate($request);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    abstract public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse;

    /**
     * @throws \InvalidArgumentException when the request is not an instance of the supported class
     */
    protected function assertSupportedRequestClass($request): void
    {
        $supportedRequestClassName = $this->supportedRequestClassName();
        if (!$request instanceof $supportedRequestClassName) {
            throw new \InvalidArgumentException(\sprintf(
                'Единственным аргументом метода "%s::validate" должен быть экземпляр класса "%s", "%s" передан.',
                \get_class($this),
                $supportedRequestClassName,
                \is_object($request) ? \get_class($request) : \get_debug_type($request),
            ));
        }
    }

    abstract protected function supportedRequestClassName(): string;
}
