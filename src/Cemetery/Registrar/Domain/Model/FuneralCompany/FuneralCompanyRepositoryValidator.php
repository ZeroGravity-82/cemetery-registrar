<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\FuneralCompany;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepository;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorRepository;
use Cemetery\Registrar\Domain\Model\Repository;
use Cemetery\Registrar\Domain\Model\RepositoryValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyRepositoryValidator implements RepositoryValidator
{
    /**
     * @param JuristicPersonRepository $juristicPersonRepo
     * @param SoleProprietorRepository $soleProprietorRepo
     * @param BurialRepository         $burialRepo
     */
    public function __construct(
        private readonly JuristicPersonRepository $juristicPersonRepo,
        private readonly SoleProprietorRepository $soleProprietorRepo,
        private readonly BurialRepository         $burialRepo,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function assertUnique(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        /** @var FuneralCompany           $aggregateRoot */
        /** @var FuneralCompanyRepository $repository */
        if ($repository->doesSameOrganizationIdAlreadyUsed($aggregateRoot)) {
            /** @var JuristicPersonId|SoleProprietorId $organizationId */
            $organizationId = $this->getOrganizationId($aggregateRoot);
            /** @var JuristicPerson|SoleProprietor $organization */
            $organization = match ($aggregateRoot->organizationId()->idType()) {
                JuristicPerson::CLASS_SHORTCUT => $this->juristicPersonRepo->findById($organizationId),
                SoleProprietor::CLASS_SHORTCUT => $this->soleProprietorRepo->findById($organizationId),
            };
            throw new \RuntimeException(\sprintf(
                'Похоронная фирма "%s" уже существует.',
                $organization->name()->value(),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assertReferencesNotBroken(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        /** @var JuristicPersonId|SoleProprietorId $organizationId */
        $organizationId = $this->getOrganizationId($aggregateRoot);
        /** @var FuneralCompany           $aggregateRoot */
        /** @var FuneralCompanyRepository $repository */
        $doesOrganizationExist = match ($aggregateRoot->organizationId()->idType()) {
            JuristicPerson::CLASS_SHORTCUT => $this->juristicPersonRepo->doesExistById($organizationId),
            SoleProprietor::CLASS_SHORTCUT => $this->soleProprietorRepo->doesExistById($organizationId),
        };
        if (!$doesOrganizationExist) {
            throw new \RuntimeException(\sprintf(
                'Организация с типом "%s" и ID "%s" не существует.',
                match ($aggregateRoot->organizationId()->idType()) {
                    JuristicPerson::CLASS_SHORTCUT => JuristicPerson::CLASS_LABEL,
                    SoleProprietor::CLASS_SHORTCUT => SoleProprietor::CLASS_LABEL,
                },
                $organizationId->value(),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assertRemovable(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        /** @var FuneralCompany           $aggregateRoot */
        /** @var FuneralCompanyRepository $repository */
        $relatedBurialCount = $this->burialRepo->countByFuneralCompanyId($aggregateRoot->id());
        if ($relatedBurialCount > 0) {
            $organization   = $this->getOrganization($aggregateRoot);
            throw new \RuntimeException(\sprintf(
                'Похоронная фирма "%s" не может быть удалена, т.к. она указана для %d захоронений.',
                $organization->name()->value(),
                $relatedBurialCount,
            ));
        }
    }

    /**
     * @param AggregateRoot $aggregateRoot
     *
     * @return JuristicPersonId|SoleProprietorId
     */
    private function getOrganizationId(AggregateRoot $aggregateRoot): JuristicPersonId|SoleProprietorId
    {
        return $aggregateRoot->organizationId()->id();
    }

    /**
     * @param AggregateRoot $aggregateRoot
     *
     * @return JuristicPerson|SoleProprietor
     */
    private function getOrganization(AggregateRoot $aggregateRoot): JuristicPerson|SoleProprietor
    {
        /** @var JuristicPersonId|SoleProprietorId $organizationId */
        $organizationId = $this->getOrganizationId($aggregateRoot);
        /** @var JuristicPerson|SoleProprietor $organization */
        $organization = match ($aggregateRoot->organizationId()->idType()) {
            JuristicPerson::CLASS_SHORTCUT => $this->juristicPersonRepo->findById($organizationId),
            SoleProprietor::CLASS_SHORTCUT => $this->soleProprietorRepo->findById($organizationId),
        };

        return $organization;
    }
}
