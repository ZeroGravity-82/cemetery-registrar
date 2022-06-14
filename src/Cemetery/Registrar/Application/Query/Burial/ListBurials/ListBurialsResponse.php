<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\Burial\ListBurials;

use Cemetery\Registrar\Domain\View\Burial\BurialList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListBurialsResponse
{
    public function __construct(
        public readonly BurialList $burialList,
    ) {}
}
