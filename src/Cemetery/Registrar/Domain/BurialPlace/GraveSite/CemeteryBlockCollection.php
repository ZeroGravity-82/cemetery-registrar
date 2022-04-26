<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\EntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CemeteryBlockCollection extends EntityCollection
{
    /**
     * {@inheritdoc}
     */
    public function supportedEntityClassName(): string
    {
        return CemeteryBlock::class;
    }
}
