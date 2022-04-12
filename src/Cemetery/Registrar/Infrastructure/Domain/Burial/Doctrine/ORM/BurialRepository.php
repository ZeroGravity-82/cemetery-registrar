<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialRepositoryInterface;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
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
        private EntityManagerInterface $entityManager,
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
        return (int) $this->entityManager
            ->getRepository(Burial::class)
            ->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere("JSON_EXTRACT(b.funeralCompanyId, '$.value') = :value")
            ->andWhere("JSON_EXTRACT(b.funeralCompanyId, '$.type') = :type")
            ->setParameter('value', $funeralCompanyId->getId()->getValue())
            ->setParameter('type', $funeralCompanyId->getIdType())
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
            ->andWhere("JSON_EXTRACT(b.customerId, '$.value') = :value")
            ->andWhere("JSON_EXTRACT(b.customerId, '$.type') = :type")
            ->setParameter('value', $customerId->getId()->getValue())
            ->setParameter('type', $customerId->getIdType())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
