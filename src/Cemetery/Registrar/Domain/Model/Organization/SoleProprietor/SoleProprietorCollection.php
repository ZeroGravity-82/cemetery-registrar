<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\AbstractEntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorCollection extends AbstractEntityCollection
{
    public function supportedEntityClassName(): string
    {
        return SoleProprietor::class;
    }
}
