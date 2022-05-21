<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialChainId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialChainIdType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = BurialChainId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'burial_chain_id';
}
