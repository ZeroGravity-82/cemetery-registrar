<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\Ogrn;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OgrnType extends CustomStringType
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
