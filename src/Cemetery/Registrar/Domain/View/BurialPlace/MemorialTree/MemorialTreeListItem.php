<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeListItem
{
    public function __construct(
        public string  $id,
        public string  $treeNumber,
        public ?string $personInChargeId,
        public ?string $personInChargeFullName,
    ) {}
}
