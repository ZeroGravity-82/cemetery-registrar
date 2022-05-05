<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeNumber;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class MemorialTreeNumberType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = MemorialTreeNumber::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'memorial_tree_number';
}
