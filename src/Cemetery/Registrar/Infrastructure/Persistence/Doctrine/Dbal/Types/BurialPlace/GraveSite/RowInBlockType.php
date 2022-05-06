<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\RowInBlock;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomIntegerType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class RowInBlockType extends CustomIntegerType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = RowInBlock::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'row_in_block';
}
