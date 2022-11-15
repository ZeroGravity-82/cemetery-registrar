<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractTransactionalApplicationService extends AbstractApplicationService
{
    public function __construct(
        private AbstractApplicationService    $service,
        private TransactionalSessionInterface $session,
    ) {}

    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        $operation = function () use ($request) {
            return $this->service->execute($request);
        };

        return $this->session->executeAtomically($operation);
    }
}
