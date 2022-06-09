<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization;

use Cemetery\Registrar\Domain\EntityMaskingId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;

/**
 * Wrapper class for value objects identifying an organization.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OrganizationId extends EntityMaskingId
{
    /**
     * @param JuristicPersonId|SoleProprietorId $id
     */
    public function __construct(
        JuristicPersonId|SoleProprietorId $id,
    ) {
        parent::__construct($id);
    }

    /**
     * {@inheritdoc}
     */
    public function idType(): string
    {
        return match (\get_class($this->id())) {
            JuristicPersonId::class => JuristicPerson::CLASS_SHORTCUT,
            SoleProprietorId::class => SoleProprietor::CLASS_SHORTCUT,
        };
    }
}
