<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeathCertificateId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeathCertificateIdType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = DeathCertificateId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'death_certificate_id';
}
