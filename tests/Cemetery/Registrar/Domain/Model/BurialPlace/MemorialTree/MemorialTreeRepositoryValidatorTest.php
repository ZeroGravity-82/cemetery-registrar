<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeNumber;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeRepositoryValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeRepositoryValidatorTest extends TestCase
{
    private MemorialTreeId                    $id;
    private MemorialTreeNumber                $treeNumber;
    private int                               $relatedBurialCount;
    private MockObject|MemorialTree           $mockMemorialTree;
    private MockObject|MemorialTree           $mockMemorialTreeTotallyDifferent;
    private MockObject|MemorialTree           $mockMemorialTreeWithSameName;
    private MockObject|MemorialTreeRepository $mockMemorialTreeRepo;
    private MemorialTreeRepositoryValidator   $validator;

    public function setUp(): void
    {
        $this->id                               = new MemorialTreeId('MT001');
        $this->treeNumber                       = new MemorialTreeNumber('001');
        $this->relatedBurialCount               = 7;
        $this->mockMemorialTree                 = $this->buildMockMemorialTree();
        $this->mockMemorialTreeTotallyDifferent = $this->buildMockMemorialTreeTotallyDifferent();
        $this->mockMemorialTreeWithSameName     = $this->buildMockMemorialTreeWithSameName();
        $this->mockMemorialTreeRepo             = $this->buildMockMemorialTreeRepo();
        $mockBurialRepo                         = $this->buildMockBurialRepo();
        $this->validator                        = new MemorialTreeRepositoryValidator($mockBurialRepo);
    }

    public function testItSuccessfullyValidatesTreeNumberUniqueness(): void
    {
        // Test it ignores the tree number of the provided entity itself
        $this->assertNull(
            $this->validator->assertUnique($this->mockMemorialTree, $this->mockMemorialTreeRepo)
        );
        // Test it successfully validates another tree number
        $this->assertNull(
            $this->validator->assertUnique($this->mockMemorialTreeTotallyDifferent, $this->mockMemorialTreeRepo)
        );
    }

    public function testItFailsWhenTreeNumberAlreadyExists(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Памятное дерево "%s" уже существует.', $this->treeNumber->value()));
        $this->validator->assertUnique($this->mockMemorialTreeWithSameName, $this->mockMemorialTreeRepo);
    }

    public function testItSuccessfullyValidatesReferencesIntegrity(): void
    {
        $this->assertNull(
            $this->validator->assertReferencesNotBroken($this->mockMemorialTree, $this->mockMemorialTreeRepo)
        );
        $this->assertNull(
            $this->validator->assertReferencesNotBroken(
                $this->mockMemorialTreeTotallyDifferent,
                $this->mockMemorialTreeRepo,
            )
        );
    }

    public function testItSuccessfullyValidatesRemovability(): void
    {
        $this->assertNull(
            $this->validator->assertRemovable($this->mockMemorialTreeTotallyDifferent, $this->mockMemorialTreeRepo)
        );
    }

    public function testItFailsWhenRemovingIsNotAllowed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Памятное дерево "%s" не может быть удалено, т.к. оно указано для %d захоронений.',
            $this->treeNumber->value(),
            $this->relatedBurialCount,

        ));
        $this->validator->assertRemovable($this->mockMemorialTree, $this->mockMemorialTreeRepo);
    }

    private function buildMockMemorialTree(): MockObject|MemorialTree
    {
        $mockMemorialTree = $this->createMock(MemorialTree::class);;
        $mockMemorialTree->method('id')->willReturn($this->id);
        $mockMemorialTree->method('treeNumber')->willReturn($this->treeNumber);

        return $mockMemorialTree;
    }

    private function buildMockMemorialTreeTotallyDifferent(): MockObject|MemorialTree
    {
        $mockMemorialTree = $this->createMock(MemorialTree::class);;
        $mockMemorialTree->method('id')->willReturn(new MemorialTreeId('MT002'));
        $mockMemorialTree->method('treeNumber')->willReturn(new MemorialTreeNumber('002'));

        return $mockMemorialTree;
    }

    private function buildMockMemorialTreeWithSameName(): MockObject|MemorialTree
    {
        $mockMemorialTree = $this->createMock(MemorialTree::class);;
        $mockMemorialTree->method('id')->willReturn(new MemorialTreeId('MT003'));
        $mockMemorialTree->method('treeNumber')->willReturn($this->treeNumber);

        return $mockMemorialTree;
    }
    
    private function buildMockMemorialTreeRepo(): MockObject|MemorialTreeRepository
    {
        $mockMemorialTreeRepo = $this->createMock(MemorialTreeRepository::class);
        $mockMemorialTreeRepo->method('doesSameTreeNumberAlreadyUsed')->willReturnCallback(
            function (MemorialTree $memorialTree) {
                return match (true) {
                    $memorialTree->id()->isEqual($this->id) => false,   // Ignore the entity itself
                    default                                 => $memorialTree->treeNumber()->isEqual($this->treeNumber),
                };
            }
        );

        return $mockMemorialTreeRepo;
    }

    private function buildMockBurialRepo(): MockObject|BurialRepository
    {
        $mockBurialRepo = $this->createMock(BurialRepository::class);
        $mockBurialRepo->method('countByMemorialTreeId')->willReturnCallback(
            function (MemorialTreeId $id) {
                return $id->isEqual($this->id) ? $this->relatedBurialCount : 0;
            }
        );

        return $mockBurialRepo;
    }
}
