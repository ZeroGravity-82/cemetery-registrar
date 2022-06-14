<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\FuneralCompany;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyNote;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyNoteTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $note = new FuneralCompanyNote('Примечание 1');
        $this->assertSame('Примечание 1', $note->value());
    }

    public function testItStringifyable(): void
    {
        $note = new FuneralCompanyNote('Примечание 1');
        $this->assertSame('Примечание 1', (string) $note);
    }

    public function testItComparable(): void
    {
        $noteA = new FuneralCompanyNote('Примечание 1');
        $noteB = new FuneralCompanyNote('Примечание 2');
        $noteC = new FuneralCompanyNote('Примечание 1');

        $this->assertFalse($noteA->isEqual($noteB));
        $this->assertTrue($noteA->isEqual($noteC));
        $this->assertFalse($noteB->isEqual($noteC));
    }
}
