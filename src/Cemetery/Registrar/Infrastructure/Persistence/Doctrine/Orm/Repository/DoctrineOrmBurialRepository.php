<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\Burial\Burial;
use Cemetery\Registrar\Domain\Model\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Model\Burial\BurialId;
use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\Burial\CustomerId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmBurialRepository extends DoctrineOrmRepository implements BurialRepository
{
    /**
     * {@inheritdoc}
     */
    public function save(Burial $burial): void
    {
        $burial->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($burial);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(BurialCollection $burials): void
    {
        foreach ($burials as $burial) {
            $burial->refreshUpdatedAtTimestamp();
            $this->entityManager->persist($burial);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(BurialId $burialId): ?Burial
    {
        return $this->entityManager->getRepository(Burial::class)->findBy([
            'id'        => $burialId->value(),
            'removedAt' => null,
        ])[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Burial $burial): void
    {
        $burial->refreshRemovedAtTimestamp();
        $this->entityManager->persist($burial);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(BurialCollection $burials): void
    {
        foreach ($burials as $burial) {
            $burial->refreshRemovedAtTimestamp();
            $this->entityManager->persist($burial);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function countByFuneralCompanyId(FuneralCompanyId $funeralCompanyId): int
    {
        return (int) $this->entityManager
            ->getRepository(Burial::class)
            ->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere('b.funeralCompanyId = :funeralCompanyId')
            ->andWhere('b.removedAt IS NULL')
            ->setParameter('funeralCompanyId', $funeralCompanyId->value())
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countByCustomerId(CustomerId $customerId): int
    {
        return (int) $this->entityManager
            ->getRepository(Burial::class)
            ->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere("JSON_EXTRACT(b.customerId, '$.type') = :type")
            ->andWhere("JSON_EXTRACT(b.customerId, '$.value') = :value")
            ->andWhere('b.removedAt IS NULL')
            ->setParameter('type', $customerId->idType())
            ->setParameter('value', $customerId->id()->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}