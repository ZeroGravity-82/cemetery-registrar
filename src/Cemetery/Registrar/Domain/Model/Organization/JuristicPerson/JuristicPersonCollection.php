<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\EntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonCollection extends EntityCollection
{
    public function supportedEntityClassName(): string
    {
        return JuristicPerson::class;
    }
}
