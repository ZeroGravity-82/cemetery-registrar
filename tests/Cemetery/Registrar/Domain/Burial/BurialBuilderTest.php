<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialBuilder;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialCodeGenerator;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\IdentityGenerator;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialBuilderTest extends TestCase
{
    private MockObject|IdentityGenerator   $mockIdentityGenerator;
    private MockObject|BurialCodeGenerator $mockBurialCodeGenerator;
    private BurialBuilder                  $burialBuilder;

    public function setUp(): void
    {
        $this->mockIdentityGenerator   = $this->createMock(IdentityGenerator::class);
        $this->mockBurialCodeGenerator = $this->createMock(BurialCodeGenerator::class);
        $this->mockIdentityGenerator->method('getNextIdentity')->willReturn('B001');
        $this->mockBurialCodeGenerator->method('getNextCode')->willReturn('000001');

        $this->burialBuilder = new BurialBuilder($this->mockIdentityGenerator, $this->mockBurialCodeGenerator);
        $this->burialBuilder->initialize(new DeceasedId('D001'), BurialType::coffinInGraveSite());
    }

    public function testItInitializesABurialWithRequiredFields(): void
    {
        $burial = $this->burialBuilder->build();

        $this->assertInstanceOf(Burial::class, $burial);
        $this->assertInstanceOf(BurialId::class, $burial->id());
        $this->assertSame('B001', (string) $burial->id());
        $this->assertInstanceOf(BurialCode::class, $burial->code());
        $this->assertSame('000001', (string) $burial->code());
        $this->assertInstanceOf(DeceasedId::class, $burial->deceasedId());
        $this->assertSame('D001', (string) $burial->deceasedId());
        $this->assertInstanceOf(BurialType::class, $burial->burialType());
        $this->assertTrue($burial->burialType()->isCoffinInGraveSite());
        $this->assertNull($burial->customerId());
        $this->assertNull($burial->burialPlaceId());
        $this->assertNull($burial->burialPlaceOwnerId());
        $this->assertNull($burial->funeralCompanyId());
        $this->assertNull($burial->burialContainer());
        $this->assertNull($burial->buriedAt());
    }

    public function testItFailsToBuildABurialBeforeInitialization(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\sprintf('Строитель для класса %s не инициализирован.', Burial::class));

        $burialBuilder = new BurialBuilder($this->mockIdentityGenerator, $this->mockBurialCodeGenerator);
        $burialBuilder->build();
    }

    public function testItAddsACustomerId(): void
    {
        $customerId = new CustomerId(new SoleProprietorId('SP001'));
        $burial     = $this->burialBuilder->addCustomerId($customerId)->build();
        $this->assertInstanceOf(CustomerId::class, $burial->customerId());
        $this->assertTrue($burial->customerId()->isEqual($customerId));
    }

    public function testItAddsABurialPlaceId(): void
    {
        $burialPlaceId = new BurialPlaceId(new GraveSiteId('GS001'));
        $burial        = $this->burialBuilder->addBurialPlaceId($burialPlaceId)->build();
        $this->assertInstanceOf(BurialPlaceId::class, $burial->burialPlaceId());
        $this->assertTrue($burial->burialPlaceId()->isEqual($burialPlaceId));
    }

    public function testItAddsABurialPlaceOwnerId(): void
    {
        $burialPlaceOwnerId = new NaturalPersonId('999');
        $burial             = $this->burialBuilder->addBurialPlaceOwnerId($burialPlaceOwnerId)->build();
        $this->assertInstanceOf(NaturalPersonId::class, $burial->burialPlaceOwnerId());
        $this->assertTrue($burial->burialPlaceOwnerId()->isEqual($burialPlaceOwnerId));
    }

    public function testItAddsAFuneralCompanyId(): void
    {
        $funeralCompanyId = new FuneralCompanyId(new SoleProprietorId('SP001'));
        $burial           = $this->burialBuilder->addFuneralCompanyId($funeralCompanyId)->build();
        $this->assertInstanceOf(FuneralCompanyId::class, $burial->funeralCompanyId());
        $this->assertTrue($burial->funeralCompanyId()->isEqual($funeralCompanyId));
    }

    public function testItAddsABurialContainer(): void
    {
        $burialContainer = new BurialContainer(new Coffin(new CoffinSize(180), CoffinShape::trapezoid(), true));
        $burial          = $this->burialBuilder->addBurialContainer($burialContainer)->build();
        $this->assertInstanceOf(BurialContainer::class, $burial->burialContainer());
        $this->assertTrue($burial->burialContainer()->isEqual($burialContainer));
    }

    public function testItAddsABuriedAt(): void
    {
        $buriedAt = new \DateTimeImmutable('2022-02-10 10:33:12');
        $burial   = $this->burialBuilder->addBuriedAt($buriedAt)->build();
        $this->assertInstanceOf(\DateTimeImmutable::class, $burial->buriedAt());
        $this->assertSame('2022-02-10 10:33:12', $burial->buriedAt()->format('Y-m-d H:i:s'));
    }
}
