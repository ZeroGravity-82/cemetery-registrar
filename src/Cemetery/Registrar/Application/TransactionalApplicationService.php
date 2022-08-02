<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class TransactionalApplicationService extends ApplicationService
{
    public function __construct(
        private readonly ApplicationService   $service,
        private readonly TransactionalSession $session,
    ) {}

    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        $operation = function () use ($request) {
            return $this->service->execute($request);
        };

        return $this->session->executeAtomically($operation);
    }
}
