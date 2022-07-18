<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface BurialRepository extends Repository
{
    /**
     * Counts burials associated with the funeral company.
     *
     * @param FuneralCompanyId $funeralCompanyId
     *
     * @return int
     */
    public function countByFuneralCompanyId(FuneralCompanyId $funeralCompanyId): int;

    /**
     * Counts burials associated with the customer.
     *
     * @param CustomerId $customerId
     *
     * @return int
     */
    public function countByCustomerId(CustomerId $customerId): int;

    /**
     * Counts burials associated with the grave site.
     *
     * @param GraveSiteId $graveSiteId
     *
     * @return int
     */
    public function countByGraveSiteId(GraveSiteId $graveSiteId): int;

    /**
     * Counts burials associated with the columbarium niche.
     *
     * @param ColumbariumNicheId $columbariumNicheId
     *
     * @return int
     */
    public function countByColumbariumNicheId(ColumbariumNicheId $columbariumNicheId): int;

    /**
     * Counts burials associated with the memorial tree.
     *
     * @param MemorialTreeId $memorialTreeId
     *
     * @return int
     */
    public function countByMemorialTreeId(MemorialTreeId $memorialTreeId): int;
}
