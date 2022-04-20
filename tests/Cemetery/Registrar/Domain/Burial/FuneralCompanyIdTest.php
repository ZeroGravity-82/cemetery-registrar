<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyIdTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $funeralCompanyId = new FuneralCompanyId(new JuristicPersonId('JP001'));
        $this->assertInstanceOf(JuristicPersonId::class, $funeralCompanyId->id());
        $this->assertSame('JP001', $funeralCompanyId->id()->value());

        $funeralCompanyId = new FuneralCompanyId(new SoleProprietorId('SP001'));
        $this->assertInstanceOf(SoleProprietorId::class, $funeralCompanyId->id());
        $this->assertSame('SP001', $funeralCompanyId->id()->value());
    }

    public function testItComparable(): void
    {
        $funeralCompanyIdA = new FuneralCompanyId(new SoleProprietorId('ID001'));
        $funeralCompanyIdB = new FuneralCompanyId(new JuristicPersonId('ID001'));
        $funeralCompanyIdC = new FuneralCompanyId(new SoleProprietorId('ID002'));
        $funeralCompanyIdD = new FuneralCompanyId(new SoleProprietorId('ID001'));

        $this->assertFalse($funeralCompanyIdA->isEqual($funeralCompanyIdB));
        $this->assertFalse($funeralCompanyIdA->isEqual($funeralCompanyIdC));
        $this->assertTrue($funeralCompanyIdA->isEqual($funeralCompanyIdD));
        $this->assertFalse($funeralCompanyIdB->isEqual($funeralCompanyIdC));
        $this->assertFalse($funeralCompanyIdB->isEqual($funeralCompanyIdD));
        $this->assertFalse($funeralCompanyIdC->isEqual($funeralCompanyIdD));
    }
}
