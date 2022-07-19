<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteNumber;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepositoryValidator;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\ColumbariumRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteRepositoryValidatorTest extends TestCase
{
    private GraveSiteId                  $id;
    private GraveSiteNumber              $nicheNumber;
    private CemeteryBlockId                       $cemeteryBlockId;
    private CemeteryBlockId                       $invalidCemeteryBlockId;
    private int                                 $relatedBurialCount;
    private MockObject|GraveSite         $mockGraveSite;
    private MockObject|GraveSite         $mockGraveSiteTotallyDifferent;
    private MockObject|GraveSite         $mockGraveSiteWithSameNumber;
    private MockObject|ColumbariumRepository    $mockGraveSiteRepo;
    private GraveSiteRepositoryValidator $validator;

    public function setUp(): void
    {
        $this->id                                                        = new GraveSiteId('CN001');
        $this->nicheNumber                                               = new GraveSiteNumber('001');
        $this->cemeteryBlockId                                             = new CemeteryBlockId('C001');
        $this->anotherCemeteryBlockId                                      = new CemeteryBlockId('C002');
        $this->invalidCemeteryBlockId                                      = new CemeteryBlockId('invalid_id');
        $this->relatedBurialCount                                        = 7;
        $this->mockGraveSite                                      = $this->buildMockGraveSite();
        $this->mockGraveSiteTotallyDifferent                      = $this->buildMockGraveSiteTotallyDifferent();
        $this->mockGraveSiteWithSameNumber                        = $this->buildMockGraveSiteWithSameNumber();
        $this->mockGraveSiteWithSameNumberButInAnotherColumbarium = $this->buildMockGraveSiteWithSameNumberButInAnotherColumbarium();
        $this->mockGraveSiteWithInvalidCemeteryBlockId              = $this->buildMockGraveSiteWithInvalidCemeteryBlockId();
        $this->mockGraveSiteRepo                                  = $this->buildMockGraveSiteRepo();
        $this->mockColumbariumRepo                                       = $this->buildMockColumbariumRepo();
        $mockBurialRepo                                                  = $this->buildMockBurialRepo();
        $this->validator                                                 = new GraveSiteRepositoryValidator(
            $this->mockColumbariumRepo,
            $mockBurialRepo,
        );
    }

    public function testItSuccessfullyValidatesNicheNumberUniqueness(): void
    {

        $this->assertNull(
            $this->validator->assertUnique($this->mockGraveSite, $this->mockGraveSiteRepo)
        );

        $this->assertNull(
            $this->validator->assertUnique($this->mockGraveSiteTotallyDifferent, $this->mockGraveSiteRepo)
        );

        $this->assertNull(
            $this->validator->assertUnique(
                $this->mockGraveSiteWithSameNumberButInAnotherColumbarium,
                $this->mockGraveSiteRepo)
        );
    }

    public function testItFailsWhenNicheNumberAlreadyUsed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Колумбарная ниша "%s" уже существует.', $this->nicheNumber->value()));
        $this->validator->assertUnique($this->mockGraveSiteWithSameNumber, $this->mockGraveSiteRepo);
    }

    public function testItSuccessfullyValidatesReferencesIntegrity(): void
    {
        $this->assertNull(
            $this->validator->assertReferencesNotBroken($this->mockGraveSite, $this->mockGraveSiteRepo)
        );
        $this->assertNull(
            $this->validator->assertReferencesNotBroken(
                $this->mockGraveSiteTotallyDifferent,
                $this->mockGraveSiteRepo,
            )
        );
    }

    public function testItFailsWhenColumbariumDoesNotExist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Колумбарий с ID "%s" не существует.', $this->invalidCemeteryBlockId));
        $this->validator->assertReferencesNotBroken(
            $this->mockGraveSiteWithInvalidCemeteryBlockId,
            $this->mockGraveSiteRepo,
        );
    }

    public function testItSuccessfullyValidatesRemovability(): void
    {
        $this->assertNull(
            $this->validator->assertRemovable(
                $this->mockGraveSiteTotallyDifferent,
                $this->mockGraveSiteRepo,
            )
        );
    }

    public function testItFailsWhenRemovingIsNotAllowed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Колумбарная ниша "%s" не может быть удалена, т.к. она указана для %d захоронений.',
            $this->nicheNumber->value(),
            $this->relatedBurialCount,

        ));
        $this->validator->assertRemovable($this->mockGraveSite, $this->mockGraveSiteRepo);
    }

    private function buildMockGraveSite(): MockObject|GraveSite
    {
        $mockGraveSite = $this->createMock(GraveSite::class);
        $mockGraveSite->method('id')->willReturn($this->id);
        $mockGraveSite->method('nicheNumber')->willReturn($this->nicheNumber);
        $mockGraveSite->method('cemeteryBlockId')->willReturn($this->cemeteryBlockId);

        return $mockGraveSite;
    }

    private function buildMockGraveSiteTotallyDifferent(): MockObject|GraveSite
    {
        $mockGraveSite = $this->createMock(GraveSite::class);
        $mockGraveSite->method('id')->willReturn(new GraveSiteId('CN002'));
        $mockGraveSite->method('nicheNumber')->willReturn(new GraveSiteNumber('002'));
        $mockGraveSite->method('cemeteryBlockId')->willReturn($this->cemeteryBlockId);

        return $mockGraveSite;
    }

    private function buildMockGraveSiteWithSameNumber(): MockObject|GraveSite
    {
        $mockGraveSite = $this->createMock(GraveSite::class);
        $mockGraveSite->method('id')->willReturn(new GraveSiteId('CN003'));
        $mockGraveSite->method('nicheNumber')->willReturn($this->nicheNumber);
        $mockGraveSite->method('cemeteryBlockId')->willReturn($this->cemeteryBlockId);

        return $mockGraveSite;
    }

    private function buildMockGraveSiteWithSameNumberButInAnotherColumbarium(): MockObject|GraveSite
    {
        $mockGraveSite = $this->createMock(GraveSite::class);
        $mockGraveSite->method('id')->willReturn(new GraveSiteId('CN003'));
        $mockGraveSite->method('nicheNumber')->willReturn($this->nicheNumber);
        $mockGraveSite->method('cemeteryBlockId')->willReturn($this->anotherCemeteryBlockId);

        return $mockGraveSite;
    }

    private function buildMockGraveSiteWithInvalidCemeteryBlockId(): MockObject|GraveSite
    {
        $mockGraveSite = $this->createMock(GraveSite::class);
        $mockGraveSite->method('id')->willReturn(new GraveSiteId('CN004'));
        $mockGraveSite->method('nicheNumber')->willReturn(new GraveSiteNumber('004'));
        $mockGraveSite->method('cemeteryBlockId')->willReturn($this->invalidCemeteryBlockId);

        return $mockGraveSite;
    }
    
    private function buildMockGraveSiteRepo(): MockObject|GraveSiteRepository
    {
        $mockGraveSiteRepo = $this->createMock(GraveSiteRepository::class);
        $mockGraveSiteRepo->method('doesSameNicheNumberAlreadyUsed')->willReturnCallback(
            function (GraveSite $graveSite) {
                return match (true) {
                    $graveSite->id()->isEqual($this->id) => false,   // Ignore the entity itself
                    default =>
                        $graveSite->cemeteryBlockId()->isEqual($this->cemeteryBlockId) &&
                        $graveSite->nicheNumber()->isEqual($this->nicheNumber),
                };
            }
        );

        return $mockGraveSiteRepo;
    }

    private function buildMockColumbariumRepo(): MockObject|ColumbariumRepository
    {
        $mockColumbariumRepo = $this->createMock(ColumbariumRepository::class);
        $mockColumbariumRepo->method('doesExistById')->willReturnCallback(
            function (CemeteryBlockId $cemeteryBlockId) {
                return $cemeteryBlockId->isEqual($this->cemeteryBlockId);
            }
        );

        return $mockColumbariumRepo;
    }

    private function buildMockBurialRepo(): MockObject|BurialRepository
    {
        $mockBurialRepo = $this->createMock(BurialRepository::class);
        $mockBurialRepo->method('countByGraveSiteId')->willReturnCallback(
            function (GraveSiteId $graveSiteId) {
                return $graveSiteId->isEqual($this->id) ? $this->relatedBurialCount : 0;
            }
        );

        return $mockBurialRepo;
    }
}
