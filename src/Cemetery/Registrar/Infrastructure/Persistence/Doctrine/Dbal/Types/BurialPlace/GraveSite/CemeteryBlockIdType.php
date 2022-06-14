<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockIdType extends CustomStringType
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
