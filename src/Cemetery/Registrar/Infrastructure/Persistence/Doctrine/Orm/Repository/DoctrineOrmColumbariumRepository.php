<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmColumbariumRepository extends DoctrineOrmRepository implements ColumbariumRepository
{
    /**
     * {@inheritdoc}
     */
    public function save(Columbarium $columbarium): void
    {
        $columbarium->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($columbarium);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(ColumbariumCollection $columbariums): void
    {
        foreach ($columbariums as $columbarium) {
            $columbarium->refreshUpdatedAtTimestamp();
            $this->entityManager->persist($columbarium);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(ColumbariumId $columbariumId): ?Columbarium
    {
        return $this->entityManager->getRepository(Columbarium::class)->findBy([
            'id'        => $columbariumId->value(),
            'removedAt' => null,
        ])[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Columbarium $columbarium): void
    {
        $columbarium->refreshRemovedAtTimestamp();
        $this->entityManager->persist($columbarium);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(ColumbariumCollection $columbariums): void
    {
        foreach ($columbariums as $columbarium) {
            $columbarium->refreshRemovedAtTimestamp();
            $this->entityManager->persist($columbarium);
        }
        $this->entityManager->flush();
    }
}
