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
        $this->assertInstanceOf(JuristicPersonId::class, $funeralCompanyId->getId());
        $this->assertSame('JP001', $funeralCompanyId->getId()->getValue());

        $funeralCompanyId = new FuneralCompanyId(new SoleProprietorId('SP001'));
        $this->assertInstanceOf(SoleProprietorId::class, $funeralCompanyId->getId());
        $this->assertSame('SP001', $funeralCompanyId->getId()->getValue());
    }

    public function testItStringifyable(): void
    {
        $funeralCompanyId = new FuneralCompanyId(new JuristicPersonId('JP001'));
        $this->assertSame(\json_encode(['value' => 'JP001', 'type' => 'JuristicPersonId']), (string) $funeralCompanyId);

        $funeralCompanyId = new FuneralCompanyId(new SoleProprietorId('SP001'));
        $this->assertSame(\json_encode(['value' => 'SP001', 'type' => 'SoleProprietorId']), (string) $funeralCompanyId);
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
