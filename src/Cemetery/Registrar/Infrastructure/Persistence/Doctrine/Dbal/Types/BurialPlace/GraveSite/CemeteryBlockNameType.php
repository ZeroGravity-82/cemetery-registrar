<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockName;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockNameType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = CemeteryBlockName::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'cemetery_block_name';
}
