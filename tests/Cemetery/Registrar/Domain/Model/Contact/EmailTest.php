<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Contact;

use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Exception;
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

        $email = new Email('новосибирск@россия.рф');
        $this->assertSame('новосибирск@россия.рф', $email->value());
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
        new Email('info@');
    }

    public function testItFailsWithInvalidFormatC(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Email('@example.com');
    }

    public function testItFailsWithInvalidFormatD(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Email('info@examplecom');
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

    public function testItChecksFormat(): void
    {
        $this->assertTrue(Email::isValidFormat('info@google.com'));
        $this->assertTrue(Email::isValidFormat('support@example.domain.com'));
        $this->assertFalse(Email::isValidFormat('info.example.com'));
        $this->assertFalse(Email::isValidFormat('info@'));
        $this->assertFalse(Email::isValidFormat('@example.com'));
        $this->assertFalse(Email::isValidFormat('info@examplecom'));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Адрес электронной почты не может иметь пустое значение.');
    }

    private function expectExceptionForInvalidFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Неверный формат адреса электронной почты.');
    }
}
