<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListCemeteryBlocks;

use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCemeteryBlocksResponse
{
    public function __construct(
        public readonly CemeteryBlockList $list,
    ) {}
}
