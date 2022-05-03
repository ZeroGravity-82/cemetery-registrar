<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\BurialPlace\ColumbariumNiche\Doctrine\ORM;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheCollection;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheRepository as ColumbariumNicheRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class ColumbariumNicheRepository implements ColumbariumNicheRepositoryInterface
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    /**
     * {@inheritdoc}
     *
     * @todo Re-throw exception about integrity constraint violation
     */
    public function save(ColumbariumNiche $columbariumNiche): void
    {
        $columbariumNiche->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($columbariumNiche);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     *
     * @todo Re-throw exception about integrity constraint violation
     */
    public function saveAll(ColumbariumNicheCollection $columbariumNiches): void
    {
        foreach ($columbariumNiches as $columbariumNiche) {
            $columbariumNiche->refreshUpdatedAtTimestamp();
            $this->entityManager->persist($columbariumNiche);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(ColumbariumNicheId $columbariumNicheId): ?ColumbariumNiche
    {
        return $this->entityManager->getRepository(ColumbariumNiche::class)->findBy([
            'id'        => $columbariumNicheId->value(),
            'removedAt' => null,
        ])[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ColumbariumNiche $columbariumNiche): void
    {
        $columbariumNiche->refreshRemovedAtTimestamp();
        $this->entityManager->persist($columbariumNiche);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(ColumbariumNicheCollection $columbariumNiches): void
    {
        foreach ($columbariumNiches as $columbariumNiche) {
            $columbariumNiche->refreshRemovedAtTimestamp();
            $this->entityManager->persist($columbariumNiche);
        }
        $this->entityManager->flush();
    }
}
