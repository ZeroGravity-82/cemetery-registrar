<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CemeteryBlockIdType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = CemeteryBlockId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'cemetery_block_id';
}
