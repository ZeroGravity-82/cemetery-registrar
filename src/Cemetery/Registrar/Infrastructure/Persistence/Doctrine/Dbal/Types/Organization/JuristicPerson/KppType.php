<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\Kpp;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class KppType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Kpp::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'kpp';
}
