<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Organization;

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
     * @return OrganizationViewList
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): OrganizationViewList;
}
