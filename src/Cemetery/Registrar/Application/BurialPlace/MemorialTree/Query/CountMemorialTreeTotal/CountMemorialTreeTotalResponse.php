<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\MemorialTree\Query\CountMemorialTreeTotal;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountMemorialTreeTotalResponse
{
    public function __construct(
        public int $totalCount,
    ) {}
}
