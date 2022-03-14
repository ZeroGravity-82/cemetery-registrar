<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\DeleteFuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Infrastructure\Domain\FuneralCompany\Doctrine\ORM\FuneralCompanyRepository;
use Cemetery\Registrar\Infrastructure\Domain\Organization\JuristicPerson\Doctrine\ORM\JuristicPersonRepository;
use Cemetery\Registrar\Infrastructure\Domain\Organization\SoleProprietor\Doctrine\ORM\SoleProprietorRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DeleteFuneralCompany
{

    public function __construct(
        private SoleProprietorRepository $soleProprietorRepo,
        private JuristicPersonRepository $juristicPersonRepo,
        private FuneralCompanyRepository $funeralCompanyRepo,
    ) {}

    /**
     * @param DeleteFuneralCompanyRequest $request
     */
    public function execute(DeleteFuneralCompanyRequest $request): void
    {
        $funeralCompany = $this->getFuneralCompany($request->funeralCompanyId);

    }

    /**
     * @param string $funeralCompanyId
     *
     * @return FuneralCompany
     *
     * @throws \RuntimeException when the funeral company does not exist.
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
