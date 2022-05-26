<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCodeGenerator;
use Cemetery\Registrar\Domain\Burial\BurialFactory;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Tests\Registrar\Domain\EntityFactoryTest;
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
        $deceasedId         = new DeceasedId('D001');
        $type               = new BurialType(BurialType::URN_IN_GRAVE_SITE);
        $customerId         = new CustomerId(new JuristicPersonId('JP001'));
        $burialPlaceId      = new BurialPlaceId(new GraveSiteId('GS001'));
        $burialPlaceOwnerId = new NaturalPersonId('NP001');
        $funeralCompanyId   = new FuneralCompanyId(new SoleProprietorId('SP001'));
        $burialContainer    = new BurialContainer(new Urn());
        $buriedAt           = new \DateTimeImmutable('2020-04-30');
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $this->mockBurialCodeGenerator->expects($this->once())->method('getNextCode');
        $burial = $this->burialFactory->create(
            $type,
            $deceasedId,
            $customerId,
            $burialPlaceId,
            $burialPlaceOwnerId,
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
        $this->assertTrue($burialPlaceOwnerId->isEqual($burial->burialPlaceOwnerId()));
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
        $this->assertNull($burial->burialPlaceOwnerId());
        $this->assertNull($burial->funeralCompanyId());
        $this->assertNull($burial->burialContainer());
        $this->assertNull($burial->burialContainer());
        $this->assertNull($burial->buriedAt());
    }
}
