<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\Burial\FuneralCompanyType;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\FuneralCompanyTypeType;

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

        $this->dbValue  = 'sole_proprietor';
        $this->phpValue = new FuneralCompanyType('sole_proprietor');
    }
}
