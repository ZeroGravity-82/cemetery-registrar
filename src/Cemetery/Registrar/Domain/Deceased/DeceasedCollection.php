<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\EntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DeceasedCollection extends EntityCollection
{
    /**
     * {@inheritdoc}
     */
    public function supportedEntityClassName(): string
    {
        return Deceased::class;
    }
}
