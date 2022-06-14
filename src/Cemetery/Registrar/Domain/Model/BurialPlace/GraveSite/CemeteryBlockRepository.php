<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface CemeteryBlockRepository
{
    /**
     * Adds the cemetery block to the repository. If the cemetery block ia already persisted, it will be updated.
     *
     * @param CemeteryBlock $cemeteryBlock
     */
    public function save(CemeteryBlock $cemeteryBlock): void;

    /**
     * Adds the collection of cemetery blocks to the repository. If any of the cemetery blocks are already persisted,
     * they will be updated.
     *
     * @param CemeteryBlockCollection $cemeteryBlocks
     */
    public function saveAll(CemeteryBlockCollection $cemeteryBlocks): void;

    /**
     * Returns the cemetery block by the ID. If no cemetery block found, null will be returned.
     *
     * @param CemeteryBlockId $cemeteryBlockId
     *
     * @return CemeteryBlock|null
     */
    public function findById(CemeteryBlockId $cemeteryBlockId): ?CemeteryBlock;

    /**
     * Removes the cemetery block from the repository.
     *
     * @param CemeteryBlock $cemeteryBlock
     */
    public function remove(CemeteryBlock $cemeteryBlock): void;

    /**
     * Removes the collection of cemetery blocks from the repository.
     *
     * @param CemeteryBlockCollection $cemeteryBlocks
     */
    public function removeAll(CemeteryBlockCollection $cemeteryBlocks): void;
}
