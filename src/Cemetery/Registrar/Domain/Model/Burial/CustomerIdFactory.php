<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CustomerIdFactory
{
    /**
     * @param NaturalPersonId|SoleProprietorId|JuristicPersonId $id
     *
     * @return CustomerId
     */
    public function create(NaturalPersonId|SoleProprietorId|JuristicPersonId $id): CustomerId
    {
        return new CustomerId($id);
    }

    /**
     * @param string|null $id
     *
     * @return CustomerId
     */
    public function createForNaturalPerson(?string $id): CustomerId
    {
        return new CustomerId(new NaturalPersonId((string) $id));
    }

    /**
     * @param string|null $id
     *
     * @return CustomerId
     */
    public function createForSoleProprietor(?string $id): CustomerId
    {
        return new CustomerId(new SoleProprietorId((string) $id));
    }

    /**
     * @param string|null $id
     *
     * @return CustomerId
     */
    public function createForJuristicPerson(?string $id): CustomerId
    {
        return new CustomerId(new JuristicPersonId((string) $id));
    }
}
