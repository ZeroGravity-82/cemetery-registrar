<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CustomerIdFactory
{
    public function create(NaturalPersonId|SoleProprietorId|JuristicPersonId $id): CustomerId
    {
        return new CustomerId($id);
    }

    /**
     * @throws Exception when the ID is invalid
     */
    public function createForNaturalPerson(?string $id): CustomerId
    {
        return new CustomerId(new NaturalPersonId((string) $id));
    }

    /**
     * @throws Exception when the ID is invalid
     */
    public function createForSoleProprietor(?string $id): CustomerId
    {
        return new CustomerId(new SoleProprietorId((string) $id));
    }

    /**
     * @throws Exception when the ID is invalid
     */
    public function createForJuristicPerson(?string $id): CustomerId
    {
        return new CustomerId(new JuristicPersonId((string) $id));
    }
}
