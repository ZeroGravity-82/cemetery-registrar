<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\CauseOfDeath;

use Cemetery\Registrar\Domain\View\FetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface CauseOfDeathFetcherInterface extends FetcherInterface
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
