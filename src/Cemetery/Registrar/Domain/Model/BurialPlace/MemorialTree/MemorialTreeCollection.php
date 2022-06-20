<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\Model\AggregateRootCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeCollection extends AggregateRootCollection
{
    /**
     * {@inheritdoc}
     */
    public function supportedClassName(): string
    {
        return MemorialTree::class;
    }
}
