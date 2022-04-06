<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\AbstractEntityIdTyped;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyId extends AbstractEntityIdTyped
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
