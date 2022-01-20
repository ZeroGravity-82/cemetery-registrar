<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyType;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyIdTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $funeralCompanyType = new FuneralCompanyType(FuneralCompanyType::JURISTIC_PERSON);
        $funeralCompanyId   = new FuneralCompanyId('777', $funeralCompanyType);
        $this->assertSame('777', $funeralCompanyId->getValue());
        $this->assertSame($funeralCompanyType, $funeralCompanyId->getType());
    }

    public function testItStringifyable(): void
    {
        $funeralCompanyType = new FuneralCompanyType(FuneralCompanyType::JURISTIC_PERSON);
        $funeralCompanyId   = new FuneralCompanyId('777', $funeralCompanyType);
        $this->assertSame(FuneralCompanyType::JURISTIC_PERSON . '.' . '777', (string) $funeralCompanyId);
    }
    
    public function testItComparable(): void
    {
        $funeralCompanyIdA = new FuneralCompanyId('777', new FuneralCompanyType(FuneralCompanyType::SOLE_PROPRIETOR));
        $funeralCompanyIdB = new FuneralCompanyId('777', new FuneralCompanyType(FuneralCompanyType::JURISTIC_PERSON));
        $funeralCompanyIdC = new FuneralCompanyId('888', new FuneralCompanyType(FuneralCompanyType::SOLE_PROPRIETOR));
        $funeralCompanyIdD = new FuneralCompanyId('777', new FuneralCompanyType(FuneralCompanyType::SOLE_PROPRIETOR));

        $this->assertFalse($funeralCompanyIdA->isEqual($funeralCompanyIdB));
        $this->assertFalse($funeralCompanyIdA->isEqual($funeralCompanyIdC));
        $this->assertTrue($funeralCompanyIdA->isEqual($funeralCompanyIdD));
        $this->assertFalse($funeralCompanyIdB->isEqual($funeralCompanyIdC));
        $this->assertFalse($funeralCompanyIdB->isEqual($funeralCompanyIdD));
        $this->assertFalse($funeralCompanyIdC->isEqual($funeralCompanyIdD));
    }
}
