<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class FuneralCompanyService
{
    /**
     * @param FuneralCompanyRepositoryInterface $funeralCompanyRepo
     */
    public function __construct(
        protected FuneralCompanyRepositoryInterface $funeralCompanyRepo,
    ) {}

    /**
     * @param string $funeralCompanyId
     *
     * @return FuneralCompany
     *
     * @throws \RuntimeException when the funeral company does not exist
     */
    protected function getFuneralCompany(string $funeralCompanyId): FuneralCompany
    {
        $funeralCompanyId = new FuneralCompanyId($funeralCompanyId);
        $funeralCompany   = $this->funeralCompanyRepo->findById($funeralCompanyId);
        if (!$funeralCompany) {
            throw new \RuntimeException('Похоронная компания с ID "%s" не найдена.');
        }

        return $funeralCompany;
    }
}
