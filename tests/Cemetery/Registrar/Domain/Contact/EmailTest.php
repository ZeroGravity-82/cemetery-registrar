<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Contact;

use Cemetery\Registrar\Domain\Contact\Email;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EmailTest extends TestCase
{
        public function testItSuccessfullyCreated(): void
    {
        $email = new Email('info@google.com');
        $this->assertSame('info@google.com', $email->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new Email('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new Email('   ');
    }

    public function testItFailsWithInvalidFormatA(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Email('info.example.com');
    }

    public function testItFailsWithInvalidFormatB(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Email('info@help@example.com');
    }

    public function testItFailsWithInvalidFormatC(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Email('info"help"@example.com');
    }

    public function testItStringifyable(): void
    {
        $email = new Email('info@google.com');
        $this->assertSame('info@google.com', (string) $email);
    }

    public function testItComparable(): void
    {
        $emailA = new Email('info@google.com');
        $emailB = new Email('support@example.domain.com');
        $emailC = new Email('info@google.com');

        $this->assertFalse($emailA->isEqual($emailB));
        $this->assertTrue($emailA->isEqual($emailC));
        $this->assertFalse($emailB->isEqual($emailC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Адрес электронной почты не может иметь пустое значение.');
    }

    private function expectExceptionForInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Адрес электронной почты имеет неверный формат.');
    }
}
