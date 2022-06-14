<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\EntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCollection extends EntityCollection
{
    /**
     * {@inheritdoc}
     */
    public function supportedEntityClassName(): string
    {
        return Burial::class;
    }
}
