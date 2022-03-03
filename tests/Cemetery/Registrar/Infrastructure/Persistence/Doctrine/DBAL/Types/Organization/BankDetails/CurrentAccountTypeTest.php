<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\CurrentAccount;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\BankDetails\CurrentAccountType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CurrentAccountTypeTest extends AbstractStringTypeTest
{
    protected string $className = CurrentAccountType::class;

    protected string $typeName = 'current_account';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '40602810700000000025';
        $this->phpValue = new CurrentAccount('40602810700000000025');
    }
}