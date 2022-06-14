<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class InnType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Inn::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'sole_proprietor_inn';
}
