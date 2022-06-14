<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorIdType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = SoleProprietorId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'sole_proprietor_id';
}
