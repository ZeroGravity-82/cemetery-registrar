<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\Organization\SoleProprietor\Doctrine\Orm;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorCollection;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorRepository;
use Cemetery\Registrar\Infrastructure\Domain\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DoctrineOrmSoleProprietorRepository extends Repository implements SoleProprietorRepository
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
            'id'        => (string) $soleProprietorId,
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