<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Model\Burial\BurialId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialIdType extends CustomStringType
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
