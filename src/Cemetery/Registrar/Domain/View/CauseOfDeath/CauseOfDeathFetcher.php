<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\CauseOfDeath;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface CauseOfDeathFetcher
{
    public const DEFAULT_PAGE_SIZE = 20;

    /**
     * @param string $id
     *
     * @return CauseOfDeathView
     *
     * @throws \RuntimeException when the cause of death is not found by ID
     */
    public function getViewById(string $id): CauseOfDeathView;

    /**
     * @param int         $page
     * @param string|null $term
     * @param int         $pageSize
     *
     * @return CauseOfDeathList
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): CauseOfDeathList;

    /**
     * @return int
     */
    public function countTotal(): int;
}
