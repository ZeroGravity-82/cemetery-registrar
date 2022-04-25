<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\EntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class MemorialTreeCollection extends EntityCollection
{
    /**
     * {@inheritdoc}
     */
    public function supportedEntityClassName(): string
    {
        return MemorialTree::class;
    }
}
