<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Deceased;

use Cemetery\Registrar\Domain\Deceased\DeathCertificateId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Deceased\DeathCertificateIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractStringTypeTest;

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

        $this->dbValue  = 'DC001';
        $this->phpValue = new DeathCertificateId('DC001');
    }
}
