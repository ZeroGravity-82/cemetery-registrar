<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class OrganizationIdFactory
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
