<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Command;

use Cemetery\Registrar\Application\ApplicationService;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class TransactionalApplicationService extends ApplicationService
{
    /**
     * @param ApplicationService   $service
     * @param TransactionalSession $session
     */
    public function __construct(
        private readonly ApplicationService   $service,
        private readonly TransactionalSession $session,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function execute($request): mixed
    {
        $operation = function () use ($request) {
            return $this->service->execute($request);
        };

        return $this->session->executeAtomically($operation);
    }
}