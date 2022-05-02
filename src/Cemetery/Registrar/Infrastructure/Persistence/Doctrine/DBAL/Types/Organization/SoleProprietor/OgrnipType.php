<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\Ogrnip;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class OgrnipType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Ogrnip::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'ogrnip';
}
