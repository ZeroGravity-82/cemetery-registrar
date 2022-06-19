<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\CauseOfDeath;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathListItem
{
    /**
     * @param string $id
     * @param string $name
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {}
}
