<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\FuneralCompany;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyName;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\FuneralCompany\FuneralCompanyNameType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyNameTypeTest extends AbstractCustomStringTypeTest
{
    protected string $className = FuneralCompanyNameType::class;
    protected string $typeName  = 'funeral_company_name';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'Апостол';
        $this->phpValue = new FuneralCompanyName('Апостол');
    }
}
