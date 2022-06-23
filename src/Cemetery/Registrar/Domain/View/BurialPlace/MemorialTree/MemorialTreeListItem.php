<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeListItem
{
    /**
     * @param string $id
     * @param string $treeNumber
     */
    public function __construct(
        public readonly string $id,
        public readonly string $treeNumber,
    ) {}
}
