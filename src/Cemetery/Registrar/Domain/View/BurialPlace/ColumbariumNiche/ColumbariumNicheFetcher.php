<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\View\Fetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface ColumbariumNicheFetcher extends Fetcher
{
    /**
     * Checks if the columbarium niche exists by the columbarium ID and the niche number.
     */
    public function doesExistByColumbariumIdAndNicheNumber(string $columbariumId, string $nicheNumber): bool;
}
