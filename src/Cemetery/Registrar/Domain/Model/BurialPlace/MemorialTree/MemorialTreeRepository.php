<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\Model\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface MemorialTreeRepository extends Repository
{
    /**
     * Checks that a memorial tree with the same tree number already exists.
     *
     * @param MemorialTree $memorialTree
     *
     * @return bool
     */
    public function doesSameTreeNumberAlreadyUsed(MemorialTree $memorialTree): bool;
}
