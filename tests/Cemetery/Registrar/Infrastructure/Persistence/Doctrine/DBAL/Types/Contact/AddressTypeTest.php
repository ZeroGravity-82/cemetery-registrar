<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Contact;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Contact\AddressType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AddressTypeTest extends AbstractStringTypeTest
{
    protected string $className = AddressType::class;

    protected string $typeName = 'address';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'г. Новосибирск, ул. 3 Интернационала, д. 127';
        $this->phpValue = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
    }
}
