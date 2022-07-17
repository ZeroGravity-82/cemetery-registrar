<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface GraveSiteRepository extends Repository
{
    /**
     * Counts grave sites associated with the cemetery block.
     *
     * @param CemeteryBlockId $cemeteryBlockId
     *
     * @return int
     */
    public function countByCemeteryBlockId(CemeteryBlockId $cemeteryBlockId): int;
}
