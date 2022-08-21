<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheListItem
{
    public function __construct(
        public string  $id,
        public string  $columbariumName,
        public int     $rowInColumbarium,
        public string  $nicheNumber,
        public ?string $personInChargeId,
        public ?string $personInChargeFullName,
    ) {}
}
