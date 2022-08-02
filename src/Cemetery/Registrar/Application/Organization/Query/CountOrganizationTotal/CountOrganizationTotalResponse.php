<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Organization\Query\CountOrganizationTotal;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountOrganizationTotalResponse
{
    public function __construct(
        public int $totalCount,
    ) {}
}
