<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\View\Fetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface CemeteryBlockFetcher extends Fetcher
{
    /**
     * Checks if the cemetery block exists by the name.
     */
    public function doesExistByName(string $name): bool;
}
