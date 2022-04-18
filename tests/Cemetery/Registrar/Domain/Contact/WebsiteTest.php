<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Contact;

use Cemetery\Registrar\Domain\Contact\Website;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class WebsiteTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $website = new Website('https://example.com');
        $this->assertSame('https://example.com', $website->value());

        $website = new Website('www.youtube.com/');
        $this->assertSame('www.youtube.com/', $website->value());

        $website = new Website('http://новый.пример.рф');
        $this->assertSame('http://новый.пример.рф', $website->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new Website('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new Website('   ');
    }

    public function testItFailsWithTooLongFullValue(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Website('https://123example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.example.com');
    }

    public function testItFailsWithTooLongLabelValue(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Website('https://exampleexampleexampleexampleexampleexampleexampleexampleexample1.com');
    }

    public function testItFailsWithInvalidFormatA(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Website('www.youtube.com.');
    }
    
    public function testItFailsWithInvalidFormatB(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Website('https://www.youtube.com/watch');
    }
    
    public function testItFailsWithInvalidFormatC(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Website('youtube');
    }

    public function testItStringifyable(): void
    {
        $coordinates = new Website('https://example.com');
        $this->assertSame('https://example.com', (string) $coordinates);
    }

    public function testItComparable(): void
    {
        $websiteA = new Website('https://example.com');
        $websiteB = new Website('пример.рф');
        $websiteC = new Website('https://example.com');

        $this->assertFalse($websiteA->isEqual($websiteB));
        $this->assertTrue($websiteA->isEqual($websiteC));
        $this->assertFalse($websiteB->isEqual($websiteC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Адрес веб-сайта не может иметь пустое значение.');
    }

    private function expectExceptionForInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Адрес веб-сайта имеет неверный формат.');
    }
}
