<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\BurialPlace\GraveSite\Doctrine\Orm;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlock;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockCollection;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockRepository as CemeteryBlockRepositoryInterface;
use Cemetery\Registrar\Infrastructure\Domain\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CemeteryBlockRepository extends Repository implements CemeteryBlockRepositoryInterface
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
