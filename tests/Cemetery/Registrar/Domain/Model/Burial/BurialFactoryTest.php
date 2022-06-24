<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\Burial\Burial;
use Cemetery\Registrar\Domain\Model\Burial\BurialCodeGenerator;
use Cemetery\Registrar\Domain\Model\Burial\BurialFactory;
use Cemetery\Registrar\Domain\Model\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Model\Burial\BurialType;
use Cemetery\Registrar\Domain\Model\Burial\CustomerId;
use Cemetery\Registrar\Domain\Model\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Model\BurialContainer\Urn;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Tests\Registrar\Domain\Model\EntityFactoryTest;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialFactoryTest extends EntityFactoryTest
{
    private const BURIAL_CODE = '10001';

    private MockObject|BurialCodeGenerator $mockBurialCodeGenerator;
    private BurialFactory                  $burialFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockBurialCodeGenerator = $this->createMock(BurialCodeGenerator::class);
        $this->mockBurialCodeGenerator->method('getNextCode')->willReturn(self::BURIAL_CODE);
        $this->burialFactory = new BurialFactory(
            $this->mockBurialCodeGenerator,
            $this->mockIdentityGenerator,
        );
    }

    public function testItCreatesBurial(): void
    {
        $deceasedId       = new DeceasedId('D001');
        $type             = new BurialType(BurialType::URN_IN_GRAVE_SITE);
        $customerId       = new CustomerId(new JuristicPersonId('JP001'));
        $burialPlaceId    = new BurialPlaceId(new GraveSiteId('GS001'));
        $personInChargeId = new NaturalPersonId('NP001');
        $funeralCompanyId = new FuneralCompanyId('FC003');
        $burialContainer  = new BurialContainer(new Urn());
        $buriedAt         = new \DateTimeImmutable('2020-04-30');
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $this->mockBurialCodeGenerator->expects($this->once())->method('getNextCode');
        $burial = $this->burialFactory->create(
            $type,
            $deceasedId,
            $customerId,
            $burialPlaceId,
            $personInChargeId,
            $funeralCompanyId,
            $burialContainer,
            $buriedAt,
        );
        $this->assertInstanceOf(Burial::class, $burial);
        $this->assertSame(self::ENTITY_ID, $burial->id()->value());
        $this->assertSame(self::BURIAL_CODE, $burial->code()->value());
        $this->assertTrue($deceasedId->isEqual($burial->deceasedId()));
        $this->assertTrue($type->isEqual($burial->type()));
        $this->assertTrue($customerId->isEqual($burial->customerId()));
        $this->assertTrue($burialPlaceId->isEqual($burial->burialPlaceId()));
        $this->assertTrue($personInChargeId->isEqual($burial->personInChargeId()));
        $this->assertTrue($funeralCompanyId->isEqual($burial->funeralCompanyId()));
        $this->assertTrue($burialContainer->isEqual($burial->burialContainer()));
        $this->assertTrue($burialContainer->isEqual($burial->burialContainer()));
        $this->assertSame(
            $buriedAt->format(\DateTimeInterface::ATOM),
            $burial->buriedAt()->format(\DateTimeInterface::ATOM)
        );
    }

    public function testItCreatesBurialWithoutOptionalFields(): void
    {
        $deceasedId         = new DeceasedId('D001');
        $type               = new BurialType(BurialType::ASHES_UNDER_MEMORIAL_TREE);
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
            null,
        );
        $this->assertInstanceOf(Burial::class, $burial);
        $this->assertSame(self::ENTITY_ID, $burial->id()->value());
        $this->assertSame(self::BURIAL_CODE, $burial->code()->value());
        $this->assertTrue($deceasedId->isEqual($burial->deceasedId()));
        $this->assertTrue($type->isEqual($burial->type()));
        $this->assertNull($burial->customerId());
        $this->assertNull($burial->burialPlaceId());
        $this->assertNull($burial->personInChargeId());
        $this->assertNull($burial->funeralCompanyId());
        $this->assertNull($burial->burialContainer());
        $this->assertNull($burial->burialContainer());
        $this->assertNull($burial->buriedAt());
    }
}
