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
     * Checks if the cause of death exists by the name.
     */
    public function doesExistByName(string $name): bool;

    /**
     * Returns a list of all causes of death.
     */
    public function findAll(?string $term = null): CauseOfDeathSimpleList;
}
