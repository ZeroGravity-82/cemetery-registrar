<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\Okpo;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OkpoType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Okpo::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'sole_proprietor_okpo';
}
