<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Contact;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Exception;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AddressTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $address = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $this->assertSame('г. Новосибирск, ул. 3 Интернационала, д. 127', $address->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new Address('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new Address('   ');
    }

    public function testItStringifyable(): void
    {
        $address = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $this->assertSame('г. Новосибирск, ул. 3 Интернационала, д. 127', (string) $address);
    }

    public function testItComparable(): void
    {
        $addressA = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $addressB = new Address('г. Москва, ул. Стандартная, д. 21, корп. 1, кв. 5');
        $addressC = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');

        $this->assertFalse($addressA->isEqual($addressB));
        $this->assertTrue($addressA->isEqual($addressC));
        $this->assertFalse($addressB->isEqual($addressC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Адрес не может иметь пустое значение.');
    }
}
