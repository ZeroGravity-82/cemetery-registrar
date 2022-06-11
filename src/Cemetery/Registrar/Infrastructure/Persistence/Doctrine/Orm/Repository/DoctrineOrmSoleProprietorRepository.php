<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorCollection;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmSoleProprietorRepository extends DoctrineOrmRepository implements SoleProprietorRepository
{
    /**
     * {@inheritdoc}
     */
    public function save(SoleProprietor $soleProprietor): void
    {
        $soleProprietor->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($soleProprietor);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(SoleProprietorCollection $soleProprietors): void
    {
        foreach ($soleProprietors as $soleProprietor) {
            $soleProprietor->refreshUpdatedAtTimestamp();
            $this->entityManager->persist($soleProprietor);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(SoleProprietorId $soleProprietorId): ?SoleProprietor
    {
        return $this->entityManager->getRepository(SoleProprietor::class)->findBy([
            'id'        => $soleProprietorId->value(),
            'removedAt' => null,
        ])[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(SoleProprietor $soleProprietor): void
    {
        $soleProprietor->refreshRemovedAtTimestamp();
        $this->entityManager->persist($soleProprietor);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(SoleProprietorCollection $soleProprietors): void
    {
        foreach ($soleProprietors as $soleProprietor) {
            $soleProprietor->refreshRemovedAtTimestamp();
            $this->entityManager->persist($soleProprietor);
        }
        $this->entityManager->flush();
    }
}
