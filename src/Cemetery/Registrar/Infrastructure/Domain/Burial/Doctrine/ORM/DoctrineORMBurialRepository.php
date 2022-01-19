<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DoctrineORMBurialRepository implements BurialRepositoryInterface
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
        $this->entityManager->persist($burial);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(BurialCollection $burials): void
    {
        foreach ($burials as $burial) {
            $this->entityManager->persist($burial);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(BurialId $burialId): ?Burial
    {
        return $this->entityManager->getRepository(Burial::class)->find((string) $burialId);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Burial $burial): void
    {
        $this->entityManager->remove($burial);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(BurialCollection $burials): void
    {
        foreach ($burials as $burial) {
            $this->entityManager->remove($burial);
        }
        $this->entityManager->flush();
    }
}
