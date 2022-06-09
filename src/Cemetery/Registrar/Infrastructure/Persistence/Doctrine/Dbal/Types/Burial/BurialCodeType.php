<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCodeType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = BurialCode::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'burial_code';
}
