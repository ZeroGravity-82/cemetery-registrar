<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\EntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumCollection extends EntityCollection
{
    /**
     * {@inheritdoc}
     */
    public function supportedEntityClassName(): string
    {
        return Columbarium::class;
    }
}
