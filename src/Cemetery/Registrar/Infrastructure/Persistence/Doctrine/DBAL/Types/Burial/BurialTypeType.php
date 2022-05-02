<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialType;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialTypeType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = BurialType::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'burial_type';
}
