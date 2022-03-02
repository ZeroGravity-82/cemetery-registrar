<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\FuneralCompanyType;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial\FuneralCompanyTypeType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyTypeTypeTest extends AbstractStringTypeTest
{
    protected string $className = FuneralCompanyTypeType::class;

    protected string $typeName = 'funeral_company_type';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'индивидуальный предприниматель';
        $this->phpValue = new FuneralCompanyType('индивидуальный предприниматель');
    }
}
