<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\Bik;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\BankDetails\BikType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\StringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BikTypeTest extends StringTypeTest
{
    protected string $className = BikType::class;
    protected string $typeName  = 'bik';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '045004774';
        $this->phpValue = new Bik('045004774');
    }
}
