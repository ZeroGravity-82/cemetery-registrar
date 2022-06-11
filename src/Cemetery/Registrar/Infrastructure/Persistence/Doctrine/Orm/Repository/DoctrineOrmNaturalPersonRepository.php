<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonCollection;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmNaturalPersonRepository extends DoctrineOrmRepository implements NaturalPersonRepository
{
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
            'id'        => $naturalPersonId->value(),
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
