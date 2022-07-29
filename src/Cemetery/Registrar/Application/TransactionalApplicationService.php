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

    /**
     * {@inheritdoc}
     */
    public function execute(ApplicationRequest $request): ApplicationResponseSuccess
    {
        $operation = function () use ($request) {
            return $this->service->execute($request);
        };

        return $this->session->executeAtomically($operation);
    }
}
