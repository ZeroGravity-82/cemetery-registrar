<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlock;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockName;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepositoryValidator;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockRepositoryValidatorTest extends TestCase
{
    private CemeteryBlockId                    $id;
    private CemeteryBlockName                  $name;
    private int                                $relatedGraveSiteCount;
    private MockObject|CemeteryBlock           $mockCemeteryBlock;
    private MockObject|CemeteryBlock           $mockCemeteryBlockTotallyDifferent;
    private MockObject|CemeteryBlock           $mockCemeteryBlockWithSameName;
    private MockObject|CemeteryBlockRepository $mockCemeteryBlockRepo;
    private CemeteryBlockRepositoryValidator     $validator;

    public function setUp(): void
    {
        $this->id                                = new CemeteryBlockId('CB001');
        $this->name                              = new CemeteryBlockName('мусульманский');
        $this->relatedGraveSiteCount             = 7;
        $this->mockCemeteryBlock                 = $this->buildMockCemeteryBlock();
        $this->mockCemeteryBlockTotallyDifferent = $this->buildMockCemeteryBlockTotallyDifferent();
        $this->mockCemeteryBlockWithSameName     = $this->buildMockCemeteryBlockWithSameName();
        $this->mockCemeteryBlockRepo             = $this->buildMockCemeteryBlockRepo();
        $mockGraveSiteRepo                       = $this->buildMockGraveSiteRepo();
        $this->validator                         = new CemeteryBlockRepositoryValidator($mockGraveSiteRepo);
    }

    public function testItValidatesUniqueName(): void
    {
        $this->assertNull(
            $this->validator->assertUnique($this->mockCemeteryBlockTotallyDifferent, $this->mockCemeteryBlockRepo)
        );
    }

    public function testItIgnoresNameOfEntityItself(): void
    {
        $this->assertNull(
            $this->validator->assertUnique($this->mockCemeteryBlock, $this->mockCemeteryBlockRepo)
        );
    }

    public function testItFailsWhenNameAlreadyExists(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Квартал "%s" уже существует.', $this->name->value()));
        $this->validator->assertUnique($this->mockCemeteryBlockWithSameName, $this->mockCemeteryBlockRepo);
    }

    public function testItValidatesReferencesIntegrity(): void
    {
        $this->assertNull(
            $this->validator->assertReferencesNotBroken($this->mockCemeteryBlock, $this->mockCemeteryBlockRepo)
        );
        $this->assertNull(
            $this->validator->assertReferencesNotBroken($this->mockCemeteryBlockTotallyDifferent, $this->mockCemeteryBlockRepo)
        );
    }

    public function testItValidatesRemovability(): void
    {
        $this->assertNull(
            $this->validator->assertRemovable($this->mockCemeteryBlockTotallyDifferent, $this->mockCemeteryBlockRepo)
        );
    }

    public function testItFailsWhenRemovingIsNotAllowed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Квартал "%s" не может быть удалён, т.к. он указан для %d участков.',
            $this->name->value(),
            $this->relatedGraveSiteCount,

        ));
        $this->validator->assertRemovable($this->mockCemeteryBlock, $this->mockCemeteryBlockRepo);
    }

    private function buildMockCemeteryBlock(): MockObject|CemeteryBlock
    {
        $mockCemeteryBlock = $this->createMock(CemeteryBlock::class);;
        $mockCemeteryBlock->method('id')->willReturn($this->id);
        $mockCemeteryBlock->method('name')->willReturn($this->name);

        return $mockCemeteryBlock;
    }

    private function buildMockCemeteryBlockTotallyDifferent(): MockObject|CemeteryBlock
    {
        $mockCemeteryBlock = $this->createMock(CemeteryBlock::class);;
        $mockCemeteryBlock->method('id')->willReturn(new CemeteryBlockId('CB002'));
        $mockCemeteryBlock->method('name')->willReturn(new CemeteryBlockName('воинский'));

        return $mockCemeteryBlock;
    }

    private function buildMockCemeteryBlockWithSameName(): MockObject|CemeteryBlock
    {
        $mockCemeteryBlock = $this->createMock(CemeteryBlock::class);;
        $mockCemeteryBlock->method('id')->willReturn(new CemeteryBlockId('CB003'));
        $mockCemeteryBlock->method('name')->willReturn($this->name);

        return $mockCemeteryBlock;
    }
    
    private function buildMockCemeteryBlockRepo(): MockObject|CemeteryBlockRepository
    {
        $mockCemeteryBlockRepo = $this->createMock(CemeteryBlockRepository::class);
        $mockCemeteryBlockRepo->method('doesSameNameAlreadyUsed')->willReturnCallback(
            function (CemeteryBlock $cemeteryBlock) {
                return match (true) {
                    $cemeteryBlock->id()->isEqual($this->id) => false,   // Ignore name of the entity itself
                    default                                  => $cemeteryBlock->name()->isEqual($this->name),
                };
            }
        );

        return $mockCemeteryBlockRepo;
    }

    private function buildMockGraveSiteRepo(): MockObject|GraveSiteRepository
    {
        $mockGraveSiteRepo = $this->createMock(GraveSiteRepository::class);
        $mockGraveSiteRepo->method('countByCemeteryBlockId')->willReturnCallback(
            function (CemeteryBlockId $id) {
                return $id->isEqual($this->id) ? $this->relatedGraveSiteCount : 0;
            }
        );

        return $mockGraveSiteRepo;
    }
}
