<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\AbstractEntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCollection extends AbstractEntityCollection
{
    public function supportedEntityClassName(): string
    {
        return Burial::class;
    }
}
