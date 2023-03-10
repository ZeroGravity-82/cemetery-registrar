<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\View\FetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface GraveSiteFetcherInterface extends FetcherInterface
{
    /**
     * Checks if the cemetery block ID, the row in the block and the position in row are already used by another
     * grave site.
     */
    public function doesAlreadyUsedCemeteryBlockIdAndRowInBlockAndPositionInRow(
        ?string $id,
        string  $cemeteryBlockId,
        int     $rowInBlock,
        ?int    $positionInRow,
    ): bool;
}
