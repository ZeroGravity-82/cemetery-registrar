<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\EntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class ColumbariumNicheCollection extends EntityCollection
{
    /**
     * {@inheritdoc}
     */
    public function supportedEntityClassName(): string
    {
        return ColumbariumNiche::class;
    }
}