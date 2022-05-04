<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\Organization\JuristicPerson\Doctrine\ORM;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonCollection;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRepositoryInterface;
use Cemetery\Registrar\Infrastructure\Domain\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class JuristicPersonRepository extends Repository implements JuristicPersonRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function save(JuristicPerson $juristicPerson): void
    {
        $juristicPerson->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($juristicPerson);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(JuristicPersonCollection $juristicPersons): void
    {
        foreach ($juristicPersons as $juristicPerson) {
            $juristicPerson->refreshUpdatedAtTimestamp();
            $this->entityManager->persist($juristicPerson);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(JuristicPersonId $juristicPersonId): ?JuristicPerson
    {
        return $this->entityManager->getRepository(JuristicPerson::class)->findBy([
            'id'        => (string) $juristicPersonId,
            'removedAt' => null,
        ])[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(JuristicPerson $juristicPerson): void
    {
        $juristicPerson->refreshRemovedAtTimestamp();
        $this->entityManager->persist($juristicPerson);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(JuristicPersonCollection $juristicPersons): void
    {
        foreach ($juristicPersons as $juristicPerson) {
            $juristicPerson->refreshRemovedAtTimestamp();
            $this->entityManager->persist($juristicPerson);
        }
        $this->entityManager->flush();
    }
}
