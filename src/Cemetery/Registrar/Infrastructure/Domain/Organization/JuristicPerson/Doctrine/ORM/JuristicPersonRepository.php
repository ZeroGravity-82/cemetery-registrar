<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\Organization\JuristicPerson\Doctrine\ORM;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonCollection;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class JuristicPersonRepository implements JuristicPersonRepositoryInterface
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
    public function save(JuristicPerson $juristicPerson): void
    {
        $this->entityManager->persist($juristicPerson);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(JuristicPersonCollection $juristicPersons): void
    {
        foreach ($juristicPersons as $juristicPerson) {
            $this->entityManager->persist($juristicPerson);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(JuristicPersonId $juristicPersonId): ?JuristicPerson
    {
        return $this->entityManager->getRepository(JuristicPerson::class)->find((string) $juristicPersonId);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(JuristicPerson $juristicPerson): void
    {
        $this->entityManager->remove($juristicPerson);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(JuristicPersonCollection $juristicPersons): void
    {
        foreach ($juristicPersons as $juristicPerson) {
            $this->entityManager->remove($juristicPerson);
        }
        $this->entityManager->flush();
    }
}