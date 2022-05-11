<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyIdFactory;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyIdFactoryTest extends TestCase
{
    private FuneralCompanyIdFactory $funeralCompanyIdFactory;

    public function setUp(): void
    {
        $this->funeralCompanyIdFactory = new FuneralCompanyIdFactory();
    }

    public function testItCreatesBurialPlaceId(): void
    {
        $funeralCompanyId = $this->funeralCompanyIdFactory->create(new SoleProprietorId('ID001'));
        $this->assertInstanceOf(FuneralCompanyId::class, $funeralCompanyId);
        $this->assertInstanceOf(SoleProprietorId::class, $funeralCompanyId->id());
        $this->assertSame('ID001', $funeralCompanyId->id()->value());

        $funeralCompanyId = $this->funeralCompanyIdFactory->create(new JuristicPersonId('ID002'));
        $this->assertInstanceOf(FuneralCompanyId::class, $funeralCompanyId);
        $this->assertInstanceOf(JuristicPersonId::class, $funeralCompanyId->id());
        $this->assertSame('ID002', $funeralCompanyId->id()->value());
    }

    public function testItCreatesBurialPlaceIdForSoleProprietor(): void
    {
        $funeralCompanyId = $this->funeralCompanyIdFactory->createForSoleProprietor('ID003');
        $this->assertInstanceOf(FuneralCompanyId::class, $funeralCompanyId);
        $this->assertInstanceOf(SoleProprietorId::class, $funeralCompanyId->id());
        $this->assertSame('ID003', $funeralCompanyId->id()->value());
    }

    public function testItCreatesBurialPlaceIdForJuristicPerson(): void
    {
        $funeralCompanyId = $this->funeralCompanyIdFactory->createForJuristicPerson('ID004');
        $this->assertInstanceOf(FuneralCompanyId::class, $funeralCompanyId);
        $this->assertInstanceOf(JuristicPersonId::class, $funeralCompanyId->id());
        $this->assertSame('ID004', $funeralCompanyId->id()->value());
    }
}
