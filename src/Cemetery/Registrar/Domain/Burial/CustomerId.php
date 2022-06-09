<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\EntityMaskingId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;

/**
 * Wrapper class for customer ID value objects.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CustomerId extends EntityMaskingId
{
    /**
     * @param NaturalPersonId|SoleProprietorId|JuristicPersonId $id
     */
    public function __construct(
        NaturalPersonId|SoleProprietorId|JuristicPersonId $id,
    ) {
        parent::__construct($id);
    }

    /**
     * {@inheritdoc}
     */
    public function idType(): string
    {
        return match (\get_class($this->id())) {
            NaturalPersonId::class  => NaturalPerson::CLASS_SHORTCUT,
            SoleProprietorId::class => SoleProprietor::CLASS_SHORTCUT,
            JuristicPersonId::class => JuristicPerson::CLASS_SHORTCUT,
        };
    }
}
