<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\AbstractEntityPolymorphicId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;

/**
 * Wrapper class for funeral company ID value objects.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyId extends AbstractEntityPolymorphicId
{
    /**
     * @param JuristicPersonId|SoleProprietorId $id
     */
    public function __construct(
        JuristicPersonId|SoleProprietorId $id,
    ) {
        parent::__construct($id);
    }
}
