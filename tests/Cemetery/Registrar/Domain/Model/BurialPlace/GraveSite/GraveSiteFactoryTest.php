<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteFactory;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepositoryInterface;
use Cemetery\Registrar\Domain\Model\NotFoundException;
use Cemetery\Tests\Registrar\Domain\Model\EntityFactoryTest;
use DataFixtures\NaturalPerson\NaturalPersonProvider;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteFactoryTest extends EntityFactoryTest
{
    private NaturalPerson    $naturalPerson;
    private GraveSiteFactory $graveSiteFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->naturalPerson    = NaturalPersonProvider::getNaturalPersonG();
        $mockNaturalPersonRepo  = $this->buildMockNaturalPersonRepo();
        $this->graveSiteFactory = new GraveSiteFactory(
            $mockNaturalPersonRepo,
            $this->mockIdentityGenerator,
        );
    }

    public function testItCreatesGraveSite(): void
    {
        $cemeteryBlockId      = 'CB001';
        $rowInBlock           = 5;
        $positionInRow        = 10;
        $geoPositionLatitude  = '54.950357';
        $geoPositionLongitude = '82.7972252';
        $geoPositionError     = '0.2';
        $size                 = '3.0';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $graveSite = $this->graveSiteFactory->create(
            $cemeteryBlockId,
            $rowInBlock,
            $positionInRow,
            $geoPositionLatitude,
            $geoPositionLongitude,
            $geoPositionError,
            $size,
            $this->naturalPerson->id()->value(),
        );
        $this->assertInstanceOf(GraveSite::class, $graveSite);
        $this->assertSame(self::ENTITY_ID, $graveSite->id()->value());
        $this->assertSame($cemeteryBlockId, $graveSite->cemeteryBlockId()->value());
        $this->assertSame($rowInBlock, $graveSite->rowInBlock()->value());
        $this->assertSame($positionInRow, $graveSite->positionInRow()->value());
        $this->assertSame($geoPositionLatitude, $graveSite->geoPosition()->coordinates()->latitude());
        $this->assertSame($geoPositionLongitude, $graveSite->geoPosition()->coordinates()->longitude());
        $this->assertSame($geoPositionError, $graveSite->geoPosition()->error()->value());
        $this->assertSame($size, $graveSite->size()->value());
        $this->assertSame($this->naturalPerson->id()->value(), $graveSite->personInChargeId()->value());
    }

    public function testItCreatesGraveSiteWithoutOptionalFields(): void
    {
        $cemeteryBlockId = 'CB001';
        $rowInBlock      = 5;
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $graveSite = $this->graveSiteFactory->create(
            $cemeteryBlockId,
            $rowInBlock,
            null,
            null,
            null,
            null,
            null,
            null,
        );
        $this->assertInstanceOf(GraveSite::class, $graveSite);
        $this->assertSame(self::ENTITY_ID, $graveSite->id()->value());
        $this->assertSame($cemeteryBlockId, $graveSite->cemeteryBlockId()->value());
        $this->assertSame($rowInBlock, $graveSite->rowInBlock()->value());
        $this->assertNull($graveSite->positionInRow());
        $this->assertNull($graveSite->geoPosition());
        $this->assertNull($graveSite->size());
        $this->assertNull($graveSite->personInChargeId());
    }

    public function testItFailsWhenPersonInChargeIsNotFoundById(): void
    {
        $cemeteryBlockId  = 'CB001';
        $rowInBlock       = 5;
        $personInChargeId = 'unknown_id';
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Физлицо с ID "%s" не найдено.', $personInChargeId));
        $this->graveSiteFactory->create(
            $cemeteryBlockId,
            $rowInBlock,
            null,
            null,
            null,
            null,
            null,
            $personInChargeId,
        );
    }

    private function buildMockNaturalPersonRepo(): MockObject|NaturalPersonRepositoryInterface
    {
        $mockNaturalPersonRepo = $this->createMock(NaturalPersonRepositoryInterface::class);
        $mockNaturalPersonRepo->method('findById')->willReturnCallback(
            function (NaturalPersonId $id) {
                return match ($id->value()) {
                    $this->naturalPerson->id()->value() => $this->naturalPerson,
                    default                             => null,
                };
            }
        );

        return $mockNaturalPersonRepo;
    }
}
