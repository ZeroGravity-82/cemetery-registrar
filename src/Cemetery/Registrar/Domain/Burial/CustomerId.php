<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\AbstractEntityPolymorphicId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CustomerId extends AbstractEntityPolymorphicId
{
    /**
     * @param NaturalPersonId|JuristicPersonId|SoleProprietorId $id
     */
    public function __construct(
        NaturalPersonId|JuristicPersonId|SoleProprietorId $id,
    ) {
        parent::__construct($id);
    }
}
