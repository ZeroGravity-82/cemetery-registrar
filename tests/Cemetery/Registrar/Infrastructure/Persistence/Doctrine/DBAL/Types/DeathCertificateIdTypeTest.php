<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\Deceased\DeathCertificateId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\DeathCertificateIdType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeathCertificateIdTypeTest extends AbstractStringTypeTest
{
    protected string $className = DeathCertificateIdType::class;

    protected string $typeName = 'death_certificate_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '4497c4a7-3717-462d-b441-35909b755498';
        $this->phpValue = new DeathCertificateId('4497c4a7-3717-462d-b441-35909b755498');
    }
}
