<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\Orm;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Infrastructure\Domain\Repository;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial\CustomerIdType;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial\FuneralCompanyIdType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DoctrineOrmBurialRepository extends Repository implements BurialRepository
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
            'id'        => (string) $burialId,
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
        $id = $funeralCompanyId->id();

        return (int) $this->entityManager
            ->getRepository(Burial::class)
            ->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere("JSON_EXTRACT(b.funeralCompanyId, '$.classShortcut') = :classShortcut")
            ->andWhere("JSON_EXTRACT(b.funeralCompanyId, '$.value') = :value")
            ->setParameter('classShortcut', $id::CLASS_SHORTCUT)
            ->setParameter('value', $id->value())
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countByCustomerId(CustomerId $customerId): int
    {
        $id = $customerId->id();

        return (int) $this->entityManager
            ->getRepository(Burial::class)
            ->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere("JSON_EXTRACT(b.customerId, '$.classShortcut') = :classShortcut")
            ->andWhere("JSON_EXTRACT(b.customerId, '$.value') = :value")
            ->setParameter('classShortcut', $id::CLASS_SHORTCUT)
            ->setParameter('value', $id->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
