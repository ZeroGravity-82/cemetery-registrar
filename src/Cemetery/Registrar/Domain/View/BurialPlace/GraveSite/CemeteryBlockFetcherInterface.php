<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\View\FetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface CemeteryBlockFetcherInterface extends FetcherInterface
{
    /**
     * Checks if the cemetery block exists by the name.
     */
    public function doesExistByName(string $name): bool;
}
