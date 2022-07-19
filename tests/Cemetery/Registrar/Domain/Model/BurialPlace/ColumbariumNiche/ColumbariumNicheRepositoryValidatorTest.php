<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheNumber;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheRepositoryValidator;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheRepositoryValidatorTest extends TestCase
{
    private ColumbariumNicheId                  $id;
    private ColumbariumNicheNumber              $nicheNumber;
    private ColumbariumId                       $columbariumId;
    private ColumbariumId                       $invalidColumbariumId;
    private int                                 $relatedBurialCount;
    private MockObject|ColumbariumNiche         $mockColumbariumNiche;
    private MockObject|ColumbariumNiche         $mockColumbariumNicheTotallyDifferent;
    private MockObject|ColumbariumNiche         $mockColumbariumNicheWithSameNumber;
    private MockObject|ColumbariumRepository    $mockColumbariumNicheRepo;
    private ColumbariumNicheRepositoryValidator $validator;

    public function setUp(): void
    {
        $this->id                                                        = new ColumbariumNicheId('CN001');
        $this->nicheNumber                                               = new ColumbariumNicheNumber('001');
        $this->columbariumId                                             = new ColumbariumId('C001');
        $this->anotherColumbariumId                                      = new ColumbariumId('C002');
        $this->invalidColumbariumId                                      = new ColumbariumId('invalid_id');
        $this->relatedBurialCount                                        = 7;
        $this->mockColumbariumNiche                                      = $this->buildMockColumbariumNiche();
        $this->mockColumbariumNicheTotallyDifferent                      = $this->buildMockColumbariumNicheTotallyDifferent();
        $this->mockColumbariumNicheWithSameNumber                        = $this->buildMockColumbariumNicheWithSameNumber();
        $this->mockColumbariumNicheWithSameNumberButInAnotherColumbarium = $this->buildMockColumbariumNicheWithSameNumberButInAnotherColumbarium();
        $this->mockColumbariumNicheWithInvalidColumbariumId              = $this->buildMockColumbariumNicheWithInvalidColumbariumId();
        $this->mockColumbariumNicheRepo                                  = $this->buildMockColumbariumNicheRepo();
        $this->mockColumbariumRepo                                       = $this->buildMockColumbariumRepo();
        $mockBurialRepo                                                  = $this->buildMockBurialRepo();
        $this->validator = new ColumbariumNicheRepositoryValidator(
            $this->mockColumbariumRepo,
            $mockBurialRepo,
        );
    }

    public function testItSuccessfullyValidatesNicheNumberUniqueness(): void
    {
        // Test it ignores the niche number of the provided entity itself
        $this->assertNull(
            $this->validator->assertUnique($this->mockColumbariumNiche, $this->mockColumbariumNicheRepo)
        );
        // Test it successfully validates another niche number
        $this->assertNull(
            $this->validator->assertUnique($this->mockColumbariumNicheTotallyDifferent, $this->mockColumbariumNicheRepo)
        );
        // Test it ignores the same niche number of another columbarium
        $this->assertNull(
            $this->validator->assertUnique(
                $this->mockColumbariumNicheWithSameNumberButInAnotherColumbarium,
                $this->mockColumbariumNicheRepo)
        );
    }

    public function testItFailsWhenNicheNumberAlreadyUsed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Колумбарная ниша "%s" уже существует.', $this->nicheNumber->value()));
        $this->validator->assertUnique($this->mockColumbariumNicheWithSameNumber, $this->mockColumbariumNicheRepo);
    }

    public function testItSuccessfullyValidatesReferencesIntegrity(): void
    {
        $this->assertNull(
            $this->validator->assertReferencesNotBroken($this->mockColumbariumNiche, $this->mockColumbariumNicheRepo)
        );
        $this->assertNull(
            $this->validator->assertReferencesNotBroken(
                $this->mockColumbariumNicheTotallyDifferent,
                $this->mockColumbariumNicheRepo,
            )
        );
    }

    public function testItFailsWhenColumbariumDoesNotExist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Колумбарий с ID "%s" не существует.', $this->invalidColumbariumId));
        $this->validator->assertReferencesNotBroken(
            $this->mockColumbariumNicheWithInvalidColumbariumId,
            $this->mockColumbariumNicheRepo,
        );
    }

    public function testItSuccessfullyValidatesRemovability(): void
    {
        $this->assertNull(
            $this->validator->assertRemovable(
                $this->mockColumbariumNicheTotallyDifferent,
                $this->mockColumbariumNicheRepo,
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
        $this->validator->assertRemovable($this->mockColumbariumNiche, $this->mockColumbariumNicheRepo);
    }

    private function buildMockColumbariumNiche(): MockObject|ColumbariumNiche
    {
        $mockColumbariumNiche = $this->createMock(ColumbariumNiche::class);
        $mockColumbariumNiche->method('id')->willReturn($this->id);
        $mockColumbariumNiche->method('nicheNumber')->willReturn($this->nicheNumber);
        $mockColumbariumNiche->method('columbariumId')->willReturn($this->columbariumId);

        return $mockColumbariumNiche;
    }

    private function buildMockColumbariumNicheTotallyDifferent(): MockObject|ColumbariumNiche
    {
        $mockColumbariumNiche = $this->createMock(ColumbariumNiche::class);
        $mockColumbariumNiche->method('id')->willReturn(new ColumbariumNicheId('CN002'));
        $mockColumbariumNiche->method('nicheNumber')->willReturn(new ColumbariumNicheNumber('002'));
        $mockColumbariumNiche->method('columbariumId')->willReturn($this->columbariumId);

        return $mockColumbariumNiche;
    }

    private function buildMockColumbariumNicheWithSameNumber(): MockObject|ColumbariumNiche
    {
        $mockColumbariumNiche = $this->createMock(ColumbariumNiche::class);
        $mockColumbariumNiche->method('id')->willReturn(new ColumbariumNicheId('CN003'));
        $mockColumbariumNiche->method('nicheNumber')->willReturn($this->nicheNumber);
        $mockColumbariumNiche->method('columbariumId')->willReturn($this->columbariumId);

        return $mockColumbariumNiche;
    }

    private function buildMockColumbariumNicheWithSameNumberButInAnotherColumbarium(): MockObject|ColumbariumNiche
    {
        $mockColumbariumNiche = $this->createMock(ColumbariumNiche::class);
        $mockColumbariumNiche->method('id')->willReturn(new ColumbariumNicheId('CN003'));
        $mockColumbariumNiche->method('nicheNumber')->willReturn($this->nicheNumber);
        $mockColumbariumNiche->method('columbariumId')->willReturn($this->anotherColumbariumId);

        return $mockColumbariumNiche;
    }

    private function buildMockColumbariumNicheWithInvalidColumbariumId(): MockObject|ColumbariumNiche
    {
        $mockColumbariumNiche = $this->createMock(ColumbariumNiche::class);
        $mockColumbariumNiche->method('id')->willReturn(new ColumbariumNicheId('CN004'));
        $mockColumbariumNiche->method('nicheNumber')->willReturn(new ColumbariumNicheNumber('004'));
        $mockColumbariumNiche->method('columbariumId')->willReturn($this->invalidColumbariumId);

        return $mockColumbariumNiche;
    }
    
    private function buildMockColumbariumNicheRepo(): MockObject|ColumbariumNicheRepository
    {
        $mockColumbariumNicheRepo = $this->createMock(ColumbariumNicheRepository::class);
        $mockColumbariumNicheRepo->method('doesSameNicheNumberAlreadyUsed')->willReturnCallback(
            function (ColumbariumNiche $columbariumNiche) {
                return match (true) {
                    $columbariumNiche->id()->isEqual($this->id) => false,   // Ignore the entity itself
                    default =>
                        $columbariumNiche->columbariumId()->isEqual($this->columbariumId) &&
                        $columbariumNiche->nicheNumber()->isEqual($this->nicheNumber),
                };
            }
        );

        return $mockColumbariumNicheRepo;
    }

    private function buildMockColumbariumRepo(): MockObject|ColumbariumRepository
    {
        $mockColumbariumRepo = $this->createMock(ColumbariumRepository::class);
        $mockColumbariumRepo->method('doesExistById')->willReturnCallback(
            function (ColumbariumId $columbariumId) {
                return $columbariumId->isEqual($this->columbariumId);
            }
        );

        return $mockColumbariumRepo;
    }

    private function buildMockBurialRepo(): MockObject|BurialRepository
    {
        $mockBurialRepo = $this->createMock(BurialRepository::class);
        $mockBurialRepo->method('countByColumbariumNicheId')->willReturnCallback(
            function (ColumbariumNicheId $columbariumNicheId) {
                return $columbariumNicheId->isEqual($this->id) ? $this->relatedBurialCount : 0;
            }
        );

        return $mockBurialRepo;
    }
}
