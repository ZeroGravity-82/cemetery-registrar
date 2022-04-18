<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\NaturalPerson\Doctrine\ORM;

use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonCollection;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class NaturalPersonRepository implements NaturalPersonRepositoryInterface
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
    public function save(NaturalPerson $naturalPerson): void
    {
        $naturalPerson->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($naturalPerson);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(NaturalPersonCollection $naturalPersons): void
    {
        foreach ($naturalPersons as $naturalPerson) {
            $naturalPerson->refreshUpdatedAtTimestamp();
            $this->entityManager->persist($naturalPerson);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(NaturalPersonId $naturalPersonId): ?NaturalPerson
    {
        return $this->entityManager->getRepository(NaturalPerson::class)->findBy([
            'id'        => (string) $naturalPersonId,
            'removedAt' => null,
        ])[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(NaturalPerson $naturalPerson): void
    {
        $naturalPerson->refreshRemovedAtTimestamp();
        $this->entityManager->persist($naturalPerson);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(NaturalPersonCollection $naturalPersons): void
    {
        foreach ($naturalPersons as $naturalPerson) {
            $naturalPerson->refreshRemovedAtTimestamp();
            $this->entityManager->persist($naturalPerson);
        }
        $this->entityManager->flush();
    }
}
