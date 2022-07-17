<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface CemeteryBlockRepository extends Repository
{
    /**
     * Checks that a cemetery block with the same name already exists.
     *
     * @param CemeteryBlock $cemeteryBlock
     *
     * @return bool
     */
    public function doesSameNameAlreadyUsed(CemeteryBlock $cemeteryBlock): bool;
}
