<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface ColumbariumNicheRepository extends Repository
{
    /**
     * Counts columbarium niches associated with the columbarium.
     *
     * @param ColumbariumId $columbariumId
     *
     * @return int
     */
    public function countByColumbariumId(ColumbariumId $columbariumId): int;

    /**
     * Checks that a columbarium niche with the same niche number already exists.
     *
     * @param ColumbariumNiche $columbariumNiche
     *
     * @return bool
     */
    public function doesSameNicheNumberAlreadyUsed(ColumbariumNiche $columbariumNiche): bool;
}
