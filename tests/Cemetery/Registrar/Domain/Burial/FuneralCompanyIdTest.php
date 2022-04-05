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
        $funeralCompanyType = FuneralCompanyType::juristicPerson();
        $funeralCompanyId   = new FuneralCompanyId('777', $funeralCompanyType);
        $this->assertSame('777', $funeralCompanyId->getValue());
        $this->assertSame($funeralCompanyType, $funeralCompanyId->getType());
    }

    public function testItStringifyable(): void
    {
        $funeralCompanyType = FuneralCompanyType::soleProprietor();
        $funeralCompanyId   = new FuneralCompanyId('777', $funeralCompanyType);
        $this->assertSame(FuneralCompanyType::SOLE_PROPRIETOR . '.777', (string) $funeralCompanyId);
    }
    
    public function testItComparable(): void
    {
        $funeralCompanyIdA = new FuneralCompanyId('777', FuneralCompanyType::soleProprietor());
        $funeralCompanyIdB = new FuneralCompanyId('777', FuneralCompanyType::juristicPerson());
        $funeralCompanyIdC = new FuneralCompanyId('888', FuneralCompanyType::soleProprietor());
        $funeralCompanyIdD = new FuneralCompanyId('777', FuneralCompanyType::soleProprietor());

        $this->assertFalse($funeralCompanyIdA->isEqual($funeralCompanyIdB));
        $this->assertFalse($funeralCompanyIdA->isEqual($funeralCompanyIdC));
        $this->assertTrue($funeralCompanyIdA->isEqual($funeralCompanyIdD));
        $this->assertFalse($funeralCompanyIdB->isEqual($funeralCompanyIdC));
        $this->assertFalse($funeralCompanyIdB->isEqual($funeralCompanyIdD));
        $this->assertFalse($funeralCompanyIdC->isEqual($funeralCompanyIdD));
    }
}
