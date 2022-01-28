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
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function save(NaturalPerson $naturalPerson): void
    {
        $this->entityManager->persist($naturalPerson);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(NaturalPersonCollection $naturalPersons): void
    {
        foreach ($naturalPersons as $naturalPerson) {
            $this->entityManager->persist($naturalPerson);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(NaturalPersonId $naturalPersonId): ?NaturalPerson
    {
        return $this->entityManager->getRepository(NaturalPerson::class)->find((string) $naturalPersonId);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(NaturalPerson $naturalPerson): void
    {
        $this->entityManager->remove($naturalPerson);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(NaturalPersonCollection $naturalPersons): void
    {
        foreach ($naturalPersons as $naturalPerson) {
            $this->entityManager->remove($naturalPerson);
        }
        $this->entityManager->flush();
    }
}
