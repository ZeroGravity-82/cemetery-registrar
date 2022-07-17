<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumName;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumRepositoryValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumRepositoryValidatorTest extends TestCase
{
    private ColumbariumId                    $id;
    private ColumbariumName                  $name;
    private int                              $relatedColumbariumNicheCount;
    private MockObject|Columbarium           $mockColumbarium;
    private MockObject|Columbarium           $mockColumbariumTotallyDifferent;
    private MockObject|Columbarium           $mockColumbariumWithSameName;
    private MockObject|ColumbariumRepository $mockColumbariumRepo;
    private ColumbariumRepositoryValidator   $validator;

    public function setUp(): void
    {
        $this->id                              = new ColumbariumId('C001');
        $this->name                            = new ColumbariumName('южный');
        $this->relatedColumbariumNicheCount    = 7;
        $this->mockColumbarium                 = $this->buildMockColumbarium();
        $this->mockColumbariumTotallyDifferent = $this->buildMockColumbariumTotallyDifferent();
        $this->mockColumbariumWithSameName     = $this->buildMockColumbariumWithSameName();
        $this->mockColumbariumRepo             = $this->buildMockColumbariumRepo();
        $mockColumbariumNicheRepo              = $this->buildMockColumbariumNicheRepo();
        $this->validator                       = new ColumbariumRepositoryValidator($mockColumbariumNicheRepo);
    }

    public function testItValidatesUniqueName(): void
    {
        $this->assertNull(
            $this->validator->assertUnique($this->mockColumbariumTotallyDifferent, $this->mockColumbariumRepo)
        );
    }

    public function testItIgnoresNameOfEntityItself(): void
    {
        $this->assertNull(
            $this->validator->assertUnique($this->mockColumbarium, $this->mockColumbariumRepo)
        );
    }

    public function testItFailsWhenNameAlreadyExists(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Колумбарий "%s" уже существует.', $this->name->value()));
        $this->validator->assertUnique($this->mockColumbariumWithSameName, $this->mockColumbariumRepo);
    }

    public function testItValidatesReferencesIntegrity(): void
    {
        $this->assertNull(
            $this->validator->assertReferencesNotBroken($this->mockColumbarium, $this->mockColumbariumRepo)
        );
        $this->assertNull(
            $this->validator->assertReferencesNotBroken($this->mockColumbariumTotallyDifferent, $this->mockColumbariumRepo)
        );
    }

    public function testItValidatesRemovability(): void
    {
        $this->assertNull(
            $this->validator->assertRemovable($this->mockColumbariumTotallyDifferent, $this->mockColumbariumRepo)
        );
    }

    public function testItFailsWhenRemovingIsNotAllowed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Колумбарий "%s" не может быть удалён, т.к. он указан для %d колумбарных ниш.',
            $this->name->value(),
            $this->relatedColumbariumNicheCount,

        ));
        $this->validator->assertRemovable($this->mockColumbarium, $this->mockColumbariumRepo);
    }

    private function buildMockColumbarium(): MockObject|Columbarium
    {
        $mockColumbarium = $this->createMock(Columbarium::class);;
        $mockColumbarium->method('id')->willReturn($this->id);
        $mockColumbarium->method('name')->willReturn($this->name);

        return $mockColumbarium;
    }

    private function buildMockColumbariumTotallyDifferent(): MockObject|Columbarium
    {
        $mockColumbarium = $this->createMock(Columbarium::class);;
        $mockColumbarium->method('id')->willReturn(new ColumbariumId('C002'));
        $mockColumbarium->method('name')->willReturn(new ColumbariumName('северный'));

        return $mockColumbarium;
    }

    private function buildMockColumbariumWithSameName(): MockObject|Columbarium
    {
        $mockColumbarium = $this->createMock(Columbarium::class);;
        $mockColumbarium->method('id')->willReturn(new ColumbariumId('C003'));
        $mockColumbarium->method('name')->willReturn($this->name);

        return $mockColumbarium;
    }
    
    private function buildMockColumbariumRepo(): MockObject|ColumbariumRepository
    {
        $mockColumbariumRepo = $this->createMock(ColumbariumRepository::class);
        $mockColumbariumRepo->method('doesSameNameAlreadyUsed')->willReturnCallback(
            function (Columbarium $columbarium) {
                return match (true) {
                    $columbarium->id()->isEqual($this->id) => false,   // Ignore name of the entity itself
                    default                                => $columbarium->name()->isEqual($this->name),
                };
            }
        );

        return $mockColumbariumRepo;
    }

    private function buildMockColumbariumNicheRepo(): MockObject|ColumbariumNicheRepository
    {
        $mockColumbariumNicheRepo = $this->createMock(ColumbariumNicheRepository::class);
        $mockColumbariumNicheRepo->method('countByColumbariumId')->willReturnCallback(
            function (ColumbariumId $id) {
                return $id->isEqual($this->id) ? $this->relatedColumbariumNicheCount : 0;
            }
        );

        return $mockColumbariumNicheRepo;
    }
}
