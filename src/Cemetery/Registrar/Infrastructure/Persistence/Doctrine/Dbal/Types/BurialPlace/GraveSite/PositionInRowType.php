<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\PositionInRow;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomIntegerType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class PositionInRowType extends CustomIntegerType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = PositionInRow::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'position_in_row';
}
