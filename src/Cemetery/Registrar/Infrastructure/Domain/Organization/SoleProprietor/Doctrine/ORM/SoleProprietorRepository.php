<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\Organization\SoleProprietor\Doctrine\ORM;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorCollection;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class SoleProprietorRepository implements SoleProprietorRepositoryInterface
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
    public function save(SoleProprietor $soleProprietor): void
    {
        $this->entityManager->persist($soleProprietor);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(SoleProprietorCollection $soleProprietors): void
    {
        foreach ($soleProprietors as $soleProprietor) {
            $this->entityManager->persist($soleProprietor);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(SoleProprietorId $soleProprietorId): ?SoleProprietor
    {
        return $this->entityManager->getRepository(SoleProprietor::class)->find((string) $soleProprietorId);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(SoleProprietor $soleProprietor): void
    {
        $this->entityManager->remove($soleProprietor);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(SoleProprietorCollection $soleProprietors): void
    {
        foreach ($soleProprietors as $soleProprietor) {
            $this->entityManager->remove($soleProprietor);
        }
        $this->entityManager->flush();
    }
}