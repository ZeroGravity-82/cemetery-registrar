<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\BankName;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\BankDetails\BankNameType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BankNameTypeTest extends AbstractStringTypeTest
{
    protected string $className = BankNameType::class;

    protected string $typeName = 'bank_name';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'АО "Тинькофф Банк"';
        $this->phpValue = new BankName('АО "Тинькофф Банк"');
    }
}
