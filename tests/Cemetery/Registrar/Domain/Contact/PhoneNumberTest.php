<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Contact;

use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PhoneNumberTest extends TestCase
{
        public function testItSuccessfullyCreated(): void
    {
        $phoneNumber = new PhoneNumber('+7-913-777-88-99');
        $this->assertSame('+7-913-777-88-99', $phoneNumber->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new PhoneNumber('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new PhoneNumber('   ');
    }

    public function testItStringifyable(): void
    {
        $phoneNumber = new PhoneNumber('+7-913-777-88-99');
        $this->assertSame('+7-913-777-88-99', (string) $phoneNumber);
    }

    public function testItComparable(): void
    {
        $phoneNumberA = new PhoneNumber('+7-913-777-88-99');
        $phoneNumberB = new PhoneNumber('8(383)123-45-67');
        $phoneNumberC = new PhoneNumber('+7-913-777-88-99');

        $this->assertFalse($phoneNumberA->isEqual($phoneNumberB));
        $this->assertTrue($phoneNumberA->isEqual($phoneNumberC));
        $this->assertFalse($phoneNumberB->isEqual($phoneNumberC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Номер телефона не может иметь пустое значение.');
    }
}
