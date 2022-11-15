<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\View\FetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface ColumbariumFetcherInterface extends FetcherInterface
{
    /**
     * Checks if the columbarium exists by the name.
     */
    public function doesExistByName(string $name): bool;
}
