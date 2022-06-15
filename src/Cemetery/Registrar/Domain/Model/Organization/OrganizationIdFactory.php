<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OrganizationIdFactory
{
    /**
     * @param SoleProprietorId|JuristicPersonId $id
     *
     * @return OrganizationId
     */
    public function create(SoleProprietorId|JuristicPersonId $id): OrganizationId
    {
        return new OrganizationId($id);
    }

    /**
     * @param string|null $id
     *
     * @return OrganizationId
     */
    public function createForSoleProprietor(?string $id): OrganizationId
    {
        return new OrganizationId(new SoleProprietorId((string) $id));
    }

    /**
     * @param string|null $id
     *
     * @return OrganizationId
     */
    public function createForJuristicPerson(?string $id): OrganizationId
    {
        return new OrganizationId(new JuristicPersonId((string) $id));
    }
}