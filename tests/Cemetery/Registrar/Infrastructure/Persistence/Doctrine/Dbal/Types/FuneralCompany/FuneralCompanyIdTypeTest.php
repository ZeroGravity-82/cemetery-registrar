<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\FuneralCompany;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\FuneralCompany\FuneralCompanyIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyIdTypeTest extends AbstractCustomStringTypeTest
{
    protected string $className = FuneralCompanyIdType::class;
    protected string $typeName  = 'funeral_company_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'FC001';
        $this->phpValue = new FuneralCompanyId('FC001');
    }
}
