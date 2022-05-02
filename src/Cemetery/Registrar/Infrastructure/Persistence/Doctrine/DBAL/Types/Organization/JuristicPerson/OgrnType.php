<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\Ogrn;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class OgrnType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Ogrn::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'ogrn';
}
