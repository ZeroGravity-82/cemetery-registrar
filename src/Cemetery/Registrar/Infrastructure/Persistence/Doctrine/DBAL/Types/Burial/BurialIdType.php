<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialIdType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = BurialId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'burial_id';
}
