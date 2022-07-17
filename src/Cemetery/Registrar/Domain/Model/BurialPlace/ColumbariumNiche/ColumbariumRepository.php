<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface ColumbariumRepository extends Repository
{
    /**
     * Checks that a columbarium with the same name already exists.
     *
     * @param Columbarium $columbarium
     *
     * @return bool
     */
    public function doesSameNameAlreadyUsed(Columbarium $columbarium): bool;
}
