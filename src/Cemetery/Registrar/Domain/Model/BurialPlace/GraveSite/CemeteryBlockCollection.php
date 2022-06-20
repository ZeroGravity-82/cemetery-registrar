<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\AggregateRootCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockCollection extends AggregateRootCollection
{
    /**
     * {@inheritdoc}
     */
    public function supportedClassName(): string
    {
        return CemeteryBlock::class;
    }
}
