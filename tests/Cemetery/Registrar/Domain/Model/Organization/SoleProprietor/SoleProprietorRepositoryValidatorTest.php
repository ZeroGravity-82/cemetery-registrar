<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorRepository;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorRepositoryValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorRepositoryValidatorTest extends TestCase
{
    private SoleProprietorRepositoryValidator   $validator;
    private MockObject|SoleProprietorRepository $mockSoleProprietorRepo;
    private MockObject|SoleProprietor           $mockSoleProprietorWithExistingName;
    private MockObject|SoleProprietor           $mockSoleProprietorWithNewName;
    private CauseOfDeathId                      $existingId;
    private CauseOfDeathName                    $existingName;
    private CauseOfDeathId                      $newId;
    private CauseOfDeathName                    $newName;
    private int                                 $relatedNaturalPersonCount;

    public function setUp(): void
    {
        $this->existingId                         = new CauseOfDeathId('CD001');
        $this->existingName                       = new CauseOfDeathName('Инфаркт');
        $this->newId                              = new CauseOfDeathId('CD002');
        $this->newName                            = new CauseOfDeathName('Астма кардиальная');
        $this->relatedNaturalPersonCount          = 7;
        $this->mockSoleProprietorRepo             = $this->buildMockSoleProprietorRepo();
        $this->mockSoleProprietorWithExistingName = $this->buildMockSoleProprietorWithExistingName();
        $this->mockSoleProprietorWithNewName      = $this->buildMockSoleProprietorWithNewName();
        $mockNaturalPersonRepo                    = $this->buildMockNaturalPersonRepo();
        $this->validator                          = new SoleProprietorRepositoryValidator($mockNaturalPersonRepo);
    }

    public function testItValidatesUniqueName(): void
    {
        $this->assertNull(
            $this->validator->assertUnique($this->mockSoleProprietorWithNewName, $this->mockSoleProprietorRepo)
        );
    }

    public function testItFailsWhenNameAlreadyUsed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('ИП "%s" уже существует.', $this->existingName->value()));
        $this->validator->assertUnique($this->mockSoleProprietorWithExistingName, $this->mockSoleProprietorRepo);
    }

    public function testItFailsWhenInnAlreadyUsed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('ИП с ИНН "%s" уже существует.', $this->existingName->value()));
        $this->validator->assertUnique($this->mockSoleProprietorWithExistingName, $this->mockSoleProprietorRepo);
    }

    public function testItValidatesReferencesExistence(): void
    {
        $this->assertNull(
            $this->validator->assertReferencesNotBroken($this->mockSoleProprietorWithNewName, $this->mockSoleProprietorRepo)
        );
    }

    public function testItValidatesRemovability(): void
    {
        $this->assertNull(
            $this->validator->assertRemovable($this->mockSoleProprietorWithNewName, $this->mockSoleProprietorRepo)
        );
    }

    public function testItFailsWhenRemovingIsNotAllowed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Причина смерти "%s" не может быть удалена, т.к. она указана для %d умерших.',
            $this->existingName->value(),
            $this->relatedNaturalPersonCount,

        ));
        $this->validator->assertRemovable($this->mockSoleProprietorWithExistingName, $this->mockSoleProprietorRepo);
    }

    private function buildMockSoleProprietorRepo(): MockObject|CauseOfDeathRepository
    {
        $mockCauseOfDeathRepo = $this->createMock(CauseOfDeathRepository::class);
        $mockCauseOfDeathRepo->method('doesNameAlreadyExist')->willReturnMap([
            [$this->existingName, true],
            [$this->newName,      false],
        ]);

        return $mockCauseOfDeathRepo;
    }

    private function buildMockNaturalPersonRepo(): MockObject|NaturalPersonRepository
    {
        $mockNaturalPersonRepo = $this->createMock(NaturalPersonRepository::class);
        $mockNaturalPersonRepo->method('countByCauseOfDeathId')->willReturnMap([
            [$this->existingId, $this->relatedNaturalPersonCount],
            [$this->newId,      0],
        ]);

        return $mockNaturalPersonRepo;
    }

    private function buildMockSoleProprietorWithExistingName(): MockObject|SoleProprietor
    {
        $mockSoleProprietor = $this->createMock(SoleProprietor::class);
        $mockSoleProprietor->method('id')->willReturn($this->existingId);
        $mockSoleProprietor->method('name')->willReturn($this->existingName);

        return $mockSoleProprietor;
    }

    private function buildMockSoleProprietorWithNewName(): MockObject|SoleProprietor
    {
        $mockSoleProprietor = $this->createMock(SoleProprietor::class);
        $mockSoleProprietor->method('id')->willReturn($this->newId);
        $mockSoleProprietor->method('name')->willReturn($this->newName);

        return $mockSoleProprietor;
    }
}
