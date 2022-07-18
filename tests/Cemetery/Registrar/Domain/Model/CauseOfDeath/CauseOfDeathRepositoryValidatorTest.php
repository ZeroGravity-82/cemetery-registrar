<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathName;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepositoryValidator;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathRepositoryValidatorTest extends TestCase
{
    private CauseOfDeathId                    $id;
    private CauseOfDeathName                  $name;
    private int                               $relatedNaturalPersonCount;
    private MockObject|CauseOfDeath           $mockCauseOfDeath;
    private MockObject|CauseOfDeath           $mockCauseOfDeathTotallyDifferent;
    private MockObject|CauseOfDeath           $mockCauseOfDeathWithSameName;
    private MockObject|CauseOfDeathRepository $mockCauseOfDeathRepo;
    private CauseOfDeathRepositoryValidator   $validator;

    public function setUp(): void
    {
        $this->id                               = new CauseOfDeathId('CD001');
        $this->name                             = new CauseOfDeathName('Инфаркт');
        $this->relatedNaturalPersonCount        = 7;
        $this->mockCauseOfDeath                 = $this->buildMockCauseOfDeath();
        $this->mockCauseOfDeathTotallyDifferent = $this->buildMockCauseOfDeathTotallyDifferent();
        $this->mockCauseOfDeathWithSameName     = $this->buildMockCauseOfDeathWithSameName();
        $this->mockCauseOfDeathRepo             = $this->buildMockCauseOfDeathRepo();
        $mockNaturalPersonRepo                  = $this->buildMockNaturalPersonRepo();
        $this->validator                        = new CauseOfDeathRepositoryValidator($mockNaturalPersonRepo);
    }

    public function testItValidatesUniqueName(): void
    {
        $this->assertNull(
            $this->validator->assertUnique($this->mockCauseOfDeathTotallyDifferent, $this->mockCauseOfDeathRepo)
        );
    }

    public function testItIgnoresNameOfEntityItself(): void
    {
        $this->assertNull(
            $this->validator->assertUnique($this->mockCauseOfDeath, $this->mockCauseOfDeathRepo)
        );
    }

    public function testItFailsWhenNameAlreadyExists(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Причина смерти "%s" уже существует.', $this->name->value()));
        $this->validator->assertUnique($this->mockCauseOfDeathWithSameName, $this->mockCauseOfDeathRepo);
    }

    public function testItValidatesReferencesIntegrity(): void
    {
        $this->assertNull(
            $this->validator->assertReferencesNotBroken($this->mockCauseOfDeath, $this->mockCauseOfDeathRepo)
        );
        $this->assertNull(
            $this->validator->assertReferencesNotBroken($this->mockCauseOfDeathTotallyDifferent, $this->mockCauseOfDeathRepo)
        );
    }

    public function testItValidatesRemovability(): void
    {
        $this->assertNull(
            $this->validator->assertRemovable($this->mockCauseOfDeathTotallyDifferent, $this->mockCauseOfDeathRepo)
        );
    }

    public function testItFailsWhenRemovingIsNotAllowed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Причина смерти "%s" не может быть удалена, т.к. она указана для %d умерших.',
            $this->name->value(),
            $this->relatedNaturalPersonCount,

        ));
        $this->validator->assertRemovable($this->mockCauseOfDeath, $this->mockCauseOfDeathRepo);
    }

    private function buildMockCauseOfDeath(): MockObject|CauseOfDeath
    {
        $mockCauseOfDeath = $this->createMock(CauseOfDeath::class);;
        $mockCauseOfDeath->method('id')->willReturn($this->id);
        $mockCauseOfDeath->method('name')->willReturn($this->name);

        return $mockCauseOfDeath;
    }

    private function buildMockCauseOfDeathTotallyDifferent(): MockObject|CauseOfDeath
    {
        $mockCauseOfDeath = $this->createMock(CauseOfDeath::class);;
        $mockCauseOfDeath->method('id')->willReturn(new CauseOfDeathId('CD002'));
        $mockCauseOfDeath->method('name')->willReturn(new CauseOfDeathName('Аста кардинальная'));

        return $mockCauseOfDeath;
    }

    private function buildMockCauseOfDeathWithSameName(): MockObject|CauseOfDeath
    {
        $mockCauseOfDeath = $this->createMock(CauseOfDeath::class);;
        $mockCauseOfDeath->method('id')->willReturn(new CauseOfDeathId('CD003'));
        $mockCauseOfDeath->method('name')->willReturn($this->name);

        return $mockCauseOfDeath;
    }
    
    private function buildMockCauseOfDeathRepo(): MockObject|CauseOfDeathRepository
    {
        $mockCauseOfDeathRepo = $this->createMock(CauseOfDeathRepository::class);
        $mockCauseOfDeathRepo->method('doesSameNameAlreadyUsed')->willReturnCallback(
            function (CauseOfDeath $causeOfDeath) {
                return match (true) {
                    $causeOfDeath->id()->isEqual($this->id) => false,   // Ignore the entity itself
                    default                                 => $causeOfDeath->name()->isEqual($this->name),
                };
            }
        );

        return $mockCauseOfDeathRepo;
    }

    private function buildMockNaturalPersonRepo(): MockObject|NaturalPersonRepository
    {
        $mockNaturalPersonRepo = $this->createMock(NaturalPersonRepository::class);
        $mockNaturalPersonRepo->method('countByCauseOfDeathId')->willReturnCallback(
            function (CauseOfDeathId $id) {
                return $id->isEqual($this->id) ? $this->relatedNaturalPersonCount : 0;
            }
        );

        return $mockNaturalPersonRepo;
    }
}
