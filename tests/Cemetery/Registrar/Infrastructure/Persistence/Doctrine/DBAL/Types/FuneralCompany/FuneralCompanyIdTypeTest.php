<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\FuneralCompany\FuneralCompanyIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyIdTypeTest extends AbstractStringTypeTest
{
    protected string $className = FuneralCompanyIdType::class;

    protected string $typeName = 'funeral_company_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'f5a29237-f99a-435b-addf-c1fd852dc6fa';
        $this->phpValue = new FuneralCompanyId('f5a29237-f99a-435b-addf-c1fd852dc6fa');
    }
}
