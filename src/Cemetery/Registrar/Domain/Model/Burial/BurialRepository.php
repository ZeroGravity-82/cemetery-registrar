<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

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
}
