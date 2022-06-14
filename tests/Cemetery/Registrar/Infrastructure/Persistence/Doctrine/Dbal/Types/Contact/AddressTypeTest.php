<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Contact;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Contact\AddressType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AddressTypeTest extends CustomStringTypeTest
{
    protected string $className = AddressType::class;
    protected string $typeName  = 'address';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'г. Новосибирск, ул. 3 Интернационала, д. 127';
        $this->phpValue = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
    }
}
