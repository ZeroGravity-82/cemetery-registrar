<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Organization;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface OrganizationFetcher
{
    public const DEFAULT_PAGE_SIZE = 20;

    /**
     * @param int         $page
     * @param string|null $term
     * @param int         $pageSize
     *
     * @return OrganizationList
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): OrganizationList;

    /**
     * @return int
     */
    public function getTotalCount(): int;
}
