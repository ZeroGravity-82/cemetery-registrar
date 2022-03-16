<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\DeleteFuneralCompany;

use Cemetery\Registrar\Domain\Burial\BurialRepositoryInterface;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DeleteFuneralCompany
{
    /**
     * @param FuneralCompanyRepositoryInterface $funeralCompanyRepo
     * @param BurialRepositoryInterface         $burialRepo
     */
    public function __construct(
        private FuneralCompanyRepositoryInterface $funeralCompanyRepo,
        private BurialRepositoryInterface         $burialRepo,
    ) {}

    /**
     * @param DeleteFuneralCompanyRequest $request
     */
    public function execute(DeleteFuneralCompanyRequest $request): void
    {
        $funeralCompany = $this->getFuneralCompany($request->funeralCompanyId);
        $hasNoBurials   = $this->burialRepo->countByFuneralCompanyId($funeralCompany->getId()) === 0;
        if ($hasNoBurials) {
            $this->funeralCompanyRepo->remove($funeralCompany);
        }
    }

    /**
     * @param string $funeralCompanyId
     *
     * @return FuneralCompany
     *
     * @throws \RuntimeException when the funeral company does not exist
     */
    private function getFuneralCompany(string $funeralCompanyId): FuneralCompany
    {
        $funeralCompanyId = new FuneralCompanyId($funeralCompanyId);
        $funeralCompany   = $this->funeralCompanyRepo->findById($funeralCompanyId);
        if (!$funeralCompany) {
            throw new \RuntimeException('Похоронная компания с ID "%s" не найдена.');
        }

        return $funeralCompany;
    }
}
