<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\Organization\CountOrganizationTotal;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountOrganizationTotalResponse
{
    /**
     * @param int $totalCount
     */
    public function __construct(
        public readonly int $totalCount,
    ) {}
}
