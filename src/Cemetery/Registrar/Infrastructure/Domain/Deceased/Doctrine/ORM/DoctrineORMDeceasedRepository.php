<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\Deceased\Doctrine\ORM;

use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedCollection;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\Deceased\DeceasedRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DoctrineORMDeceasedRepository implements DeceasedRepositoryInterface
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
    public function save(Deceased $deceased): void
    {
        $this->entityManager->persist($deceased);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(DeceasedCollection $deceaseds): void
    {
        foreach ($deceaseds as $deceased) {
            $this->entityManager->persist($deceased);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(DeceasedId $deceasedId): ?Deceased
    {
        return $this->entityManager->getRepository(Deceased::class)->find((string) $deceasedId);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Deceased $deceased): void
    {
        $this->entityManager->remove($deceased);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(DeceasedCollection $deceaseds): void
    {
        foreach ($deceaseds as $deceased) {
            $this->entityManager->remove($deceased);
        }
        $this->entityManager->flush();
    }
}
