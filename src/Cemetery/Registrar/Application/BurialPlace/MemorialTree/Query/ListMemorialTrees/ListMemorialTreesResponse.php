<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\MemorialTree\Query\ListMemorialTrees;

use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListMemorialTreesResponse
{
    public function __construct(
        public MemorialTreeList $list,
    ) {}
}
