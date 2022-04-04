<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRemover;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class RemoveFuneralCompanyService extends FuneralCompanyService
{
    /**
     * @param FuneralCompanyRemover             $funeralCompanyRemover
     * @param FuneralCompanyRepositoryInterface $funeralCompanyRepo
     */
    public function __construct(
        private FuneralCompanyRemover     $funeralCompanyRemover,
        FuneralCompanyRepositoryInterface $funeralCompanyRepo,
    ) {
        parent::__construct($funeralCompanyRepo);
    }

    /**
     * @param RemoveFuneralCompanyRequest $request
     */
    public function execute(RemoveFuneralCompanyRequest $request): void
    {
        $funeralCompany = $this->getFuneralCompany($request->funeralCompanyId);
        $this->funeralCompanyRemover->remove($funeralCompany);
    }
}
