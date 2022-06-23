<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface ColumbariumFetcher
{
    /**
     * @param string $id
     *
     * @return ColumbariumView
     *
     * @throws \RuntimeException when the columbarium is not found by ID
     */
    public function getViewById(string $id): ColumbariumView;

    /**
     * @return ColumbariumList
     */
    public function findAll(): ColumbariumList;

    /**
     * @return int
     */
    public function countTotal(): int;
}
