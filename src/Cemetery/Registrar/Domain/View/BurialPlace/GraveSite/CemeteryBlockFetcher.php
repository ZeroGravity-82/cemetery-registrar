<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface CemeteryBlockFetcher
{
    /**
     * @param string $id
     *
     * @return CemeteryBlockView
     *
     * @throws \RuntimeException when the cemetery block is not found by ID
     */
    public function getViewById(string $id): CemeteryBlockView;

    /**
     * @return CemeteryBlockList
     */
    public function findAll(): CemeteryBlockList;

    /**
     * @return int
     */
    public function countTotal(): int;
}
