<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\View\Fetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface ColumbariumFetcher extends Fetcher
{
    /**
     * Checks if the columbarium exists by the name.
     */
    public function doesExistByName(string $name): bool;
}
