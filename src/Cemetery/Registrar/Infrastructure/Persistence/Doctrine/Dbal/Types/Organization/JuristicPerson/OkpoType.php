<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\Okpo;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class OkpoType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Okpo::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'juristic_person_okpo';
}
