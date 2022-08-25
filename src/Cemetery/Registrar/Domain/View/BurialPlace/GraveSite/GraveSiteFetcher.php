<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\View\Fetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface GraveSiteFetcher extends Fetcher
{
    /**
     * Checks if the grave site exists by the cemetery block ID, the row in the block and the position in row.
     */
    public function doesExistByCemeteryBlockIdAndRowInBlockAndPositionInRow(
        string $cemeteryBlockId,
        int    $rowInBlock,
        ?int   $positionInRow,
    ): bool;
}
