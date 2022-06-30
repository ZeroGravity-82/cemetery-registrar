<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedIdType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = DeceasedId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'deceased_id';
}
