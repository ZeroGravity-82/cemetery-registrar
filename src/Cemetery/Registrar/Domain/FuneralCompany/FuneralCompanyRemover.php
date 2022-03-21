<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\Burial\BurialRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyRemover
{
    /**
     * @param BurialRepositoryInterface         $burialRepo
     * @param FuneralCompanyRepositoryInterface $funeralCompanyRepo
     */
    public function __construct(
        private BurialRepositoryInterface         $burialRepo,
        private FuneralCompanyRepositoryInterface $funeralCompanyRepo,
    ) {}

    /**
     * @param FuneralCompany $funeralCompany
     */
    public function remove(FuneralCompany $funeralCompany): void
    {
        $burialCount = $this->burialRepo->countByFuneralCompanyId($funeralCompany->getId());
        if ($burialCount > 0) {
            throw new \RuntimeException(\sprintf(
                'Похоронная компания не может быть удалена, т.к. она связана с %d захоронениями.',
                $burialCount,
            ));
        }
        $this->funeralCompanyRepo->remove($funeralCompany);
    }
}
