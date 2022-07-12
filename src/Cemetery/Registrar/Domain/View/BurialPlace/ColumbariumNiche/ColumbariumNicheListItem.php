<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheListItem
{
    /**
     * @param string $id
     * @param string $columbariumName
     * @param int    $rowInColumbarium
     * @param string $nicheNumber
     */
    public function __construct(
        public readonly string $id,
        public readonly string $columbariumName,
        public readonly int    $rowInColumbarium,
        public readonly string $nicheNumber,
    ) {}
}
