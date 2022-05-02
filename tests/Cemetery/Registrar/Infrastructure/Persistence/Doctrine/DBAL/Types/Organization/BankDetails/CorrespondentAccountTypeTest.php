<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\CorrespondentAccount;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\BankDetails\CorrespondentAccountType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\StringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CorrespondentAccountTypeTest extends StringTypeTest
{
    protected string $className = CorrespondentAccountType::class;
    protected string $typeName  = 'correspondent_account';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '30101810600000000774';
        $this->phpValue = new CorrespondentAccount('30101810600000000774');
    }
}
