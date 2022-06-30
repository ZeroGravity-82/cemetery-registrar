<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\CremationCertificateId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CremationCertificateIdType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = CremationCertificateId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'cremation_certificate_id';
}
