<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Deceased;

use Cemetery\Registrar\Domain\Deceased\DeathCertificateId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DeathCertificateIdType extends CustomStringType
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
