<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlock;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmCemeteryBlockRepository extends DoctrineOrmRepository implements CemeteryBlockRepository
{
    /**
     * {@inheritdoc}
     *
     * @todo Re-throw exception about integrity constraint violation
     */
    public function save(CemeteryBlock $cemeteryBlock): void
    {
        $cemeteryBlock->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($cemeteryBlock);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     *
     * @todo Re-throw exception about integrity constraint violation
     */
    public function saveAll(CemeteryBlockCollection $cemeteryBlocks): void
    {
        foreach ($cemeteryBlocks as $cemeteryBlock) {
            $cemeteryBlock->refreshUpdatedAtTimestamp();
            $this->entityManager->persist($cemeteryBlock);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(CemeteryBlockId $cemeteryBlockId): ?CemeteryBlock
    {
        return $this->entityManager->getRepository(CemeteryBlock::class)->findBy([
            'id'        => $cemeteryBlockId->value(),
            'removedAt' => null,
        ])[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(CemeteryBlock $cemeteryBlock): void
    {
        $cemeteryBlock->refreshRemovedAtTimestamp();
        $this->entityManager->persist($cemeteryBlock);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(CemeteryBlockCollection $cemeteryBlocks): void
    {
        foreach ($cemeteryBlocks as $cemeteryBlock) {
            $cemeteryBlock->refreshRemovedAtTimestamp();
            $this->entityManager->persist($cemeteryBlock);
        }
        $this->entityManager->flush();
    }
}
