<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\CurrentAccount;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\BankDetails\CurrentAccountType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CurrentAccountTypeTest extends CustomStringTypeTest
{
    protected string $className = CurrentAccountType::class;
    protected string $typeName  = 'current_account';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '40602810700000000025';
        $this->phpValue = new CurrentAccount('40602810700000000025');
    }
}
