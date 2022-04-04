<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyDeleter;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DeleteFuneralCompanyService extends FuneralCompanyService
{
    /**
     * @param FuneralCompanyDeleter             $funeralCompanyDeleter
     * @param FuneralCompanyRepositoryInterface $funeralCompanyRepo
     */
    public function __construct(
        private FuneralCompanyDeleter     $funeralCompanyDeleter,
        FuneralCompanyRepositoryInterface $funeralCompanyRepo,
    ) {
        parent::__construct($funeralCompanyRepo);
    }

    /**
     * @param DeleteFuneralCompanyRequest $request
     */
    public function execute(DeleteFuneralCompanyRequest $request): void
    {
        $funeralCompany = $this->getFuneralCompany($request->funeralCompanyId);
        $this->funeralCompanyDeleter->delete($funeralCompany);
    }
}
