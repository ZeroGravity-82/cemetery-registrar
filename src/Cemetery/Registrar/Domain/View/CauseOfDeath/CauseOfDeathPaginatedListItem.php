<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\CauseOfDeath;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathPaginatedListItem
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}
