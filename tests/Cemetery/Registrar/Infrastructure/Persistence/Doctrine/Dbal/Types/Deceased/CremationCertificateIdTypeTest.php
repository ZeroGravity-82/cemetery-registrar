<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Deceased;

use Cemetery\Registrar\Domain\Model\Deceased\CremationCertificateId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Deceased\CremationCertificateIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CremationCertificateIdTypeTest extends CustomStringTypeTest
{
    protected string $className = CremationCertificateIdType::class;
    protected string $typeName  = 'cremation_certificate_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'CC001';
        $this->phpValue = new CremationCertificateId('CC001');
    }
}
