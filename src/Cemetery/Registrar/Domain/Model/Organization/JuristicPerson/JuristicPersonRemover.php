<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\Burial\CustomerId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyRepository;
use Cemetery\Registrar\Domain\Model\Organization\OrganizationId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonRemover
{
    /**
     * @param FuneralCompanyRepository $funeralCompanyRepo
     * @param BurialRepository         $burialRepo
     * @param JuristicPersonRepository $juristicPersonRepo
     */
    public function __construct(
        private readonly FuneralCompanyRepository $funeralCompanyRepo,
        private readonly BurialRepository         $burialRepo,
        private readonly JuristicPersonRepository $juristicPersonRepo,
    ) {}

    /**
     * @param JuristicPerson $juristicPerson
     */
    public function remove(JuristicPerson $juristicPerson): void
    {
        $funeralCompany = $this->funeralCompanyRepo->findByOrganizationId(new OrganizationId($juristicPerson->id()));
        if ($funeralCompany) {
            throw new \RuntimeException(\sprintf(
                'Юридическое лицо не может быть удалено, т.к. оно связано с похоронной фирмой с ID "%s".',
                $funeralCompany->id()->value(),
            ));
        }
        $burialCount = $this->burialRepo->countByCustomerId(new CustomerId($juristicPerson->id()));
        if ($burialCount > 0) {
            throw new \RuntimeException(\sprintf(
                'Юридическое лицо не может быть удалено, т.к. оно указано как заказчик для %d захоронений.',
                $burialCount,
            ));
        }
        $this->juristicPersonRepo->remove($juristicPerson);
    }
}
