<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\View\Fetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface MemorialTreeFetcher extends Fetcher
{
    /**
     * Checks if the memorial tree exists by the tree number.
     */
    public function doesExistByTreeNumber(string $treeNumber): bool;
}
