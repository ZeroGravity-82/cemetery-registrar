<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\View;

use Cemetery\Registrar\Domain\View\DateTimeFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DateTimeFormatterTest extends TestCase
{
    public function testItHasValidDateFormatConstant(): void
    {
        $this->assertSame('d.m.Y', DateTimeFormatter::DATE_FORMAT);
    }

    public function testItHasValidDateTimeFormatConstant(): void
    {
        $this->assertSame('d.m.Y H:i', DateTimeFormatter::DATETIME_FORMAT);
    }

    public function testItFormatsDate(): void
    {
        $formatter = new DateTimeFormatter;
        $value     = new \DateTimeImmutable('2022-01-23 17:15');
        $this->assertSame('23.01.2022', $formatter->formatDate($value));
    }

    public function testItFormatsDateTime(): void
    {
        $formatter = new DateTimeFormatter;
        $value     = new \DateTimeImmutable('2022-01-23 17:15');
        $this->assertSame('23.01.2022 17:15', $formatter->formatDateTime($value));
    }
}
