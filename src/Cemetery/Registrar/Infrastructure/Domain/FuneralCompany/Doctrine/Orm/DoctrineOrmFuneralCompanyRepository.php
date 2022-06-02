<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\FuneralCompany\Doctrine\Orm;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyCollection;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRepository;
use Cemetery\Registrar\Domain\Organization\OrganizationId;
use Cemetery\Registrar\Infrastructure\Domain\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DoctrineOrmFuneralCompanyRepository extends Repository implements FuneralCompanyRepository
{
    /**
     * {@inheritdoc}
     */
    public function save(FuneralCompany $funeralCompany): void
    {
        $funeralCompany->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($funeralCompany);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(FuneralCompanyCollection $funeralCompanies): void
    {
        foreach ($funeralCompanies as $funeralCompany) {
            $funeralCompany->refreshUpdatedAtTimestamp();
            $this->entityManager->persist($funeralCompany);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(FuneralCompanyId $funeralCompanyId): ?FuneralCompany
    {
        return $this->entityManager->getRepository(FuneralCompany::class)->findBy([
            'id'        => $funeralCompanyId->value(),
            'removedAt' => null,
        ])[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(FuneralCompany $funeralCompany): void
    {
        $funeralCompany->refreshRemovedAtTimestamp();
        $this->entityManager->persist($funeralCompany);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(FuneralCompanyCollection $funeralCompanies): void
    {
        foreach ($funeralCompanies as $funeralCompany) {
            $funeralCompany->refreshRemovedAtTimestamp();
            $this->entityManager->persist($funeralCompany);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findByOrganizationId(OrganizationId $organizationId): ?FuneralCompany
    {
        return $this->entityManager->getRepository(Burial::class)->findBy([
            'id'        => (string) $burialId,
            'removedAt' => null,
        ])[0] ?? null;
    }
}
