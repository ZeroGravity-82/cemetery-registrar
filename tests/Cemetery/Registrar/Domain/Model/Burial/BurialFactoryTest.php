<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\Burial\Burial;
use Cemetery\Registrar\Domain\Model\Burial\BurialCodeGeneratorInterface;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Urn;
use Cemetery\Registrar\Domain\Model\Burial\BurialFactory;
use Cemetery\Registrar\Domain\Model\Burial\BurialType;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Tests\Registrar\Domain\Model\EntityFactoryTest;
use DataFixtures\BurialPlace\GraveSite\GraveSiteProvider;
use DataFixtures\BurialPlace\MemorialTree\MemorialTreeProvider;
use DataFixtures\Organization\JuristicPerson\JuristicPersonProvider;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialFactoryTest extends EntityFactoryTest
{
    private const BURIAL_CODE = '10001';

    private MockObject|BurialCodeGeneratorInterface $mockBurialCodeGenerator;
    private BurialFactory                           $burialFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockBurialCodeGenerator = $this->createMock(BurialCodeGeneratorInterface::class);
        $this->mockBurialCodeGenerator->method('getNextCode')->willReturn(self::BURIAL_CODE);
        $this->burialFactory = new BurialFactory(
            $this->mockBurialCodeGenerator,
            $this->mockIdentityGenerator,
        );
    }

    public function testItCreatesBurialForCoffin(): void
    {
        $deceasedId       = new NaturalPersonId('NP002');
        $type             = new BurialType(BurialType::COFFIN_IN_GRAVE_SITE);
        $customer         = JuristicPersonProvider::getJuristicPersonA();
        $burialPlace      = GraveSiteProvider::getGraveSiteA();
        $funeralCompanyId = new FuneralCompanyId('FC003');
        $burialContainer  = new BurialContainer(new Coffin(new CoffinSize(180), CoffinShape::greekWithoutHandles(), false));
        $buriedAt         = new \DateTimeImmutable('2020-04-30');
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $this->mockBurialCodeGenerator->expects($this->once())->method('getNextCode');
        $burial = $this->burialFactory->create(
            $type,
            $deceasedId,
            $customer,
            $burialPlace,
            $funeralCompanyId,
            $burialContainer,
            $buriedAt,
        );
        $this->assertInstanceOf(Burial::class, $burial);
        $this->assertSame(self::ENTITY_ID, $burial->id()->value());
        $this->assertSame(self::BURIAL_CODE, $burial->code()->value());
        $this->assertTrue($deceasedId->isEqual($burial->deceasedId()));
        $this->assertTrue($type->isEqual($burial->type()));
        $this->assertTrue($customer->id()->isEqual($burial->customerId()));
        $this->assertTrue($burialPlace->id()->isEqual($burial->burialPlaceId()));
        $this->assertTrue($funeralCompanyId->isEqual($burial->funeralCompanyId()));
        $this->assertTrue($burialContainer->isEqual($burial->burialContainer()));
        $this->assertSame(
            $buriedAt->format(\DateTimeInterface::ATOM),
            $burial->buriedAt()->format(\DateTimeInterface::ATOM)
        );
    }

    public function testItCreatesBurialForUrn(): void
    {
        $deceasedId      = new NaturalPersonId('NP002');
        $type            = new BurialType(BurialType::URN_IN_GRAVE_SITE);
        $customer        = JuristicPersonProvider::getJuristicPersonA();
        $burialPlace     = GraveSiteProvider::getGraveSiteA();
        $burialContainer = new BurialContainer(new Urn());
        $buriedAt        = new \DateTimeImmutable('2018-01-24');
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $this->mockBurialCodeGenerator->expects($this->once())->method('getNextCode');
        $burial = $this->burialFactory->create(
            $type,
            $deceasedId,
            $customer,
            $burialPlace,
            null,
            $burialContainer,
            $buriedAt,
        );
        $this->assertInstanceOf(Burial::class, $burial);
        $this->assertSame(self::ENTITY_ID, $burial->id()->value());
        $this->assertSame(self::BURIAL_CODE, $burial->code()->value());
        $this->assertTrue($deceasedId->isEqual($burial->deceasedId()));
        $this->assertTrue($type->isEqual($burial->type()));
        $this->assertTrue($customer->id()->isEqual($burial->customerId()));
        $this->assertTrue($burialPlace->id()->isEqual($burial->burialPlaceId()));
        $this->assertNull($burial->funeralCompanyId());
        $this->assertTrue($burialContainer->isEqual($burial->burialContainer()));
        $this->assertSame(
            $buriedAt->format(\DateTimeInterface::ATOM),
            $burial->buriedAt()->format(\DateTimeInterface::ATOM)
        );
    }

    public function testItCreatesBurialForAshes(): void
    {
        $deceasedId  = new NaturalPersonId('NP002');
        $type        = new BurialType(BurialType::ASHES_UNDER_MEMORIAL_TREE);
        $customer    = JuristicPersonProvider::getJuristicPersonA();
        $burialPlace = MemorialTreeProvider::getMemorialTreeA();
        $buriedAt    = new \DateTimeImmutable('1998-11-11');
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $this->mockBurialCodeGenerator->expects($this->once())->method('getNextCode');
        $burial = $this->burialFactory->create(
            $type,
            $deceasedId,
            $customer,
            $burialPlace,
            null,
            null,
            $buriedAt,
        );
        $this->assertInstanceOf(Burial::class, $burial);
        $this->assertSame(self::ENTITY_ID, $burial->id()->value());
        $this->assertSame(self::BURIAL_CODE, $burial->code()->value());
        $this->assertTrue($deceasedId->isEqual($burial->deceasedId()));
        $this->assertTrue($type->isEqual($burial->type()));
        $this->assertTrue($customer->id()->isEqual($burial->customerId()));
        $this->assertTrue($burialPlace->id()->isEqual($burial->burialPlaceId()));
        $this->assertNull($burial->funeralCompanyId());
        $this->assertNull($burial->burialContainer());
        $this->assertSame(
            $buriedAt->format(\DateTimeInterface::ATOM),
            $burial->buriedAt()->format(\DateTimeInterface::ATOM)
        );
    }

    public function testItCreatesBurialWithoutOptionalFields(): void
    {
        $deceasedId = new NaturalPersonId('NP002');
        $type       = new BurialType(BurialType::ASHES_UNDER_MEMORIAL_TREE);
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $this->mockBurialCodeGenerator->expects($this->once())->method('getNextCode');
        $burial = $this->burialFactory->create(
            $type,
            $deceasedId,
            null,
            null,
            null,
            null,
            null,
        );
        $this->assertInstanceOf(Burial::class, $burial);
        $this->assertSame(self::ENTITY_ID, $burial->id()->value());
        $this->assertSame(self::BURIAL_CODE, $burial->code()->value());
        $this->assertTrue($deceasedId->isEqual($burial->deceasedId()));
        $this->assertTrue($type->isEqual($burial->type()));
        $this->assertNull($burial->customerId());
        $this->assertNull($burial->burialPlaceId());
        $this->assertNull($burial->funeralCompanyId());
        $this->assertNull($burial->burialContainer());
        $this->assertNull($burial->burialContainer());
        $this->assertNull($burial->buriedAt());
    }
}
