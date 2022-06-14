<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyCollection;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyRepository;
use Cemetery\Registrar\Domain\Model\Organization\OrganizationId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmFuneralCompanyRepository extends DoctrineOrmRepository implements FuneralCompanyRepository
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
    public function findByOrganizationId(OrganizationId $organizationId): ?FuneralCompany
    {
        return $this->entityManager
            ->getRepository(FuneralCompany::class)
            ->createQueryBuilder('fc')
            ->andWhere("JSON_EXTRACT(fc.organizationId, '$.type') = :type")
            ->andWhere("JSON_EXTRACT(fc.organizationId, '$.value') = :value")
            ->andWhere('fc.removedAt IS NULL')
            ->setParameter('type', $organizationId->idType())
            ->setParameter('value', $organizationId->id()->value())
            ->getQuery()
            ->getOneOrNullResult();
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
}
