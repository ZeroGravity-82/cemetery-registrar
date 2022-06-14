<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Contact;

use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Contact\PhoneNumberType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PhoneNumberTypeTest extends CustomStringTypeTest
{
    protected string $className = PhoneNumberType::class;
    protected string $typeName  = 'phone_number';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '+7-913-777-88-99';
        $this->phpValue = new PhoneNumber('+7-913-777-88-99');
    }
}
