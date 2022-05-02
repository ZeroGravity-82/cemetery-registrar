<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class InnType extends CustomStringType
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
