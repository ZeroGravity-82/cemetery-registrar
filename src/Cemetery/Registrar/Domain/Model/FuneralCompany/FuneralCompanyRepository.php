<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\FuneralCompany;

use Cemetery\Registrar\Domain\Model\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Model\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface FuneralCompanyRepository extends Repository
{
    /**
     * Returns the funeral company by organization ID. If no funeral company found, null will be returned.
     *
     * @param OrganizationId $organizationId
     *
     * @return FuneralCompany|null
     */
    public function findByOrganizationId(OrganizationId $organizationId): ?FuneralCompany;
}
