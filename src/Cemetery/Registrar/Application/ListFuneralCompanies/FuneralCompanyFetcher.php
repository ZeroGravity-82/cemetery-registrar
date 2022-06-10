<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\ListFuneralCompanies;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface FuneralCompanyFetcher
{
    public const DEFAULT_PAGE_SIZE = 20;

    /**
     * @param int         $page
     * @param string|null $term
     * @param int         $pageSize
     *
     * @return FuneralCompanyViewList
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): FuneralCompanyViewList;
}
