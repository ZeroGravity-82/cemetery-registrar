<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialRepositoryInterface;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial\CustomerIdType;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial\FuneralCompanyIdType;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialRepository implements BurialRepositoryInterface
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

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
            ->andWhere("JSON_EXTRACT(b.funeralCompanyId, '$.class') = :class")
            ->andWhere("JSON_EXTRACT(b.funeralCompanyId, '$.value') = :value")
            ->setParameter('class', FuneralCompanyIdType::ID_CLASS_NAMES[$id::class])
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
            ->andWhere("JSON_EXTRACT(b.customerId, '$.class') = :class")
            ->andWhere("JSON_EXTRACT(b.customerId, '$.value') = :value")
            ->setParameter('class', CustomerIdType::ID_CLASS_NAMES[$id::class])
            ->setParameter('value', $id->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
