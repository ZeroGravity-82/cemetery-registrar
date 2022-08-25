<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\CauseOfDeath;

use Cemetery\Registrar\Domain\View\Fetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface CauseOfDeathFetcher extends Fetcher
{
    /**
     * Checks if the entity exists by the name.
     */
    public function doesExistByName(string $name): bool;
}
