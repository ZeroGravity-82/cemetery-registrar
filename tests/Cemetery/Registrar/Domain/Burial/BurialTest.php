<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTreeId;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Tests\Registrar\Domain\AbstractAggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialTest extends AbstractAggregateRootTest
{
    private Burial $burial;

    public function setUp(): void
    {
        $id           = new BurialId('B001');
        $burialCode   = new BurialCode('BC001');
        $deceasedId   = new DeceasedId('D001');
        $burialType   = BurialType::coffinInGraveSite();
        $this->burial = new Burial($id, $burialCode, $deceasedId, $burialType);
        $this->entity = $this->burial;
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(BurialId::class, $this->burial->id());
        $this->assertSame('B001', (string) $this->burial->id());
        $this->assertInstanceOf(BurialCode::class, $this->burial->code());
        $this->assertSame('BC001', (string) $this->burial->code());
        $this->assertInstanceOf(DeceasedId::class, $this->burial->deceasedId());
        $this->assertSame('D001', (string) $this->burial->deceasedId());
        $this->assertInstanceOf(BurialType::class, $this->burial->burialType());
        $this->assertTrue($this->burial->burialType()->isCoffinInGraveSite());
        $this->assertNull($this->burial->customerId());
        $this->assertNull($this->burial->burialPlaceId());
        $this->assertNull($this->burial->burialPlaceOwnerId());
        $this->assertNull($this->burial->funeralCompanyId());
        $this->assertNull($this->burial->burialContainer());
        $this->assertNull($this->burial->buriedAt());
    }

    public function testItSetsDeceasedId(): void
    {
        $deceasedId = new DeceasedId('D001');
        $this->burial->setDeceasedId($deceasedId);
        $this->assertTrue($this->burial->deceasedId()->isEqual($deceasedId));
    }

    public function testItSetsBurialType(): void
    {
        $burialType = BurialType::urnInColumbariumNiche();
        $this->burial->setBurialType($burialType);
        $this->assertTrue($this->burial->burialType()->isEqual($burialType));
    }

    public function testItSetsCustomerId(): void
    {
        $naturalPersonId = new NaturalPersonId('NP001');
        $customerId      = new CustomerId($naturalPersonId);
        $this->burial->setCustomerId($customerId);
        $this->assertInstanceOf(CustomerId::class, $this->burial->customerId());
        $this->assertInstanceOf(NaturalPersonId::class, $this->burial->customerId()->id());
        $this->assertSame('NP001', $this->burial->customerId()->id()->value());

        $juristicPersonId = new JuristicPersonId('JP001');
        $customerId       = new CustomerId($juristicPersonId);
        $this->burial->setCustomerId($customerId);
        $this->assertInstanceOf(CustomerId::class, $this->burial->customerId());
        $this->assertInstanceOf(JuristicPersonId::class, $this->burial->customerId()->id());
        $this->assertSame('JP001', $this->burial->customerId()->id()->value());

        $soleProprietorId = new SoleProprietorId('SP001');
        $customerId       = new CustomerId($soleProprietorId);
        $this->burial->setCustomerId($customerId);
        $this->assertInstanceOf(CustomerId::class, $this->burial->customerId());
        $this->assertInstanceOf(SoleProprietorId::class, $this->burial->customerId()->id());
        $this->assertSame('SP001', $this->burial->customerId()->id()->value());
    }

    public function testItSetsBurialPlaceId(): void
    {
        $graveSiteId   = new GraveSiteId('GS001');
        $burialPlaceId = new BurialPlaceId($graveSiteId);
        $this->burial->setBurialPlaceId($burialPlaceId);
        $this->assertInstanceOf(BurialPlaceId::class, $this->burial->burialPlaceId());
        $this->assertInstanceOf(GraveSiteId::class, $this->burial->burialPlaceId()->id());
        $this->assertSame('GS001', $this->burial->burialPlaceId()->id()->value());

        $columbariumNicheId = new ColumbariumNicheId('CN001');
        $burialPlaceId      = new BurialPlaceId($columbariumNicheId);
        $this->burial->setBurialPlaceId($burialPlaceId);
        $this->assertInstanceOf(BurialPlaceId::class, $this->burial->burialPlaceId());
        $this->assertInstanceOf(ColumbariumNicheId::class, $this->burial->burialPlaceId()->id());
        $this->assertSame('CN001', $this->burial->burialPlaceId()->id()->value());

        $memorialTreeId = new MemorialTreeId('MT001');
        $burialPlaceId  = new BurialPlaceId($memorialTreeId);
        $this->burial->setBurialPlaceId($burialPlaceId);
        $this->assertInstanceOf(BurialPlaceId::class, $this->burial->burialPlaceId());
        $this->assertInstanceOf(MemorialTreeId::class, $this->burial->burialPlaceId()->id());
        $this->assertSame('MT001', $this->burial->burialPlaceId()->id()->value());
    }

    public function testItSetsBurialPlaceOwnerId(): void
    {
        $burialPlaceOwnerId = new NaturalPersonId('NP002');
        $this->burial->setBurialPlaceOwnerId($burialPlaceOwnerId);
        $this->assertInstanceOf(NaturalPersonId::class, $this->burial->burialPlaceOwnerId());
        $this->assertSame('NP002', (string) $this->burial->burialPlaceOwnerId());
    }
    
    public function testItSetsFuneralCompanyId(): void
    {
        $juristicPersonId = new JuristicPersonId('JP001');
        $funeralCompanyId = new FuneralCompanyId($juristicPersonId);
        $this->burial->setFuneralCompanyId($funeralCompanyId);
        $this->assertInstanceOf(FuneralCompanyId::class, $this->burial->funeralCompanyId());
        $this->assertInstanceOf(JuristicPersonId::class, $this->burial->funeralCompanyId()->id());
        $this->assertSame('JP001', $this->burial->funeralCompanyId()->id()->value());

        $soleProprietorId = new SoleProprietorId('SP001');
        $funeralCompanyId = new FuneralCompanyId($soleProprietorId);
        $this->burial->setFuneralCompanyId($funeralCompanyId);
        $this->assertInstanceOf(FuneralCompanyId::class, $this->burial->funeralCompanyId());
        $this->assertInstanceOf(SoleProprietorId::class, $this->burial->funeralCompanyId()->id());
        $this->assertSame('SP001', $this->burial->funeralCompanyId()->id()->value());
    }

    public function testItSetsBurialContainer(): void
    {
        $burialContainer = new BurialContainer(new Coffin(new CoffinSize(180), CoffinShape::american(), false));
        $this->burial->setBurialContainer($burialContainer);
        $this->assertInstanceOf(BurialContainer::class, $this->burial->burialContainer());
        $this->assertTrue($this->burial->burialContainer()->isEqual($burialContainer));
    }
    
    public function testItSetsBuriedAt(): void
    {
        $buriedAt = new \DateTimeImmutable('2022-01-01 01:01:01');
        $this->burial->setBuriedAt($buriedAt);
        $this->assertSame('2022-01-01 01:01:01', $this->burial->buriedAt()->format('Y-m-d H:i:s'));
    }
}
