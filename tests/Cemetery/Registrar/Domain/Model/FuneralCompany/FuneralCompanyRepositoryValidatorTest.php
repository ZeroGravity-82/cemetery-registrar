<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\FuneralCompany;

use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyRepository;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyRepositoryValidator;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepository;
use Cemetery\Registrar\Domain\Model\Organization\Name;
use Cemetery\Registrar\Domain\Model\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyRepositoryValidatorTest extends TestCase
{
    private FuneralCompanyId                    $id;
    private OrganizationId                      $organizationId;
    private OrganizationId                      $anotherOrganizationId;
    private OrganizationId                      $invalidOrganizationId;
    private int                                 $relatedBurialCount;
    private MockObject|FuneralCompany           $mockFuneralCompany;
    private MockObject|FuneralCompany           $mockFuneralCompanyTotallyDifferent;
    private MockObject|FuneralCompany           $mockFuneralCompanyWithSameOrganizationId;
    private MockObject|FuneralCompanyRepository $mockFuneralCompanyRepo;
    private MockObject|JuristicPerson           $mockJuristicPerson;
    private FuneralCompanyRepositoryValidator   $validator;

    public function setUp(): void
    {
        $this->id                                          = new FuneralCompanyId('FC001');
        $this->organizationId                              = new OrganizationId(new JuristicPersonId('JP001'));
        $this->anotherOrganizationId                       = new OrganizationId(new SoleProprietorId('SP001'));
        $this->invalidOrganizationId                       = new OrganizationId(new JuristicPersonId('invalid_id'));
        $this->relatedBurialCount                          = 7;
        $this->mockFuneralCompany                          = $this->buildMockFuneralCompany();
        $this->mockFuneralCompanyTotallyDifferent          = $this->buildMockFuneralCompanyTotallyDifferent();
        $this->mockFuneralCompanyWithSameOrganizationId    = $this->buildMockFuneralCompanyWithSameOrganizationId();
        $this->mockFuneralCompanyWithInvalidOrganizationId = $this->buildMockFuneralCompanyWithInvalidOrganizationId();
        $this->mockFuneralCompanyRepo                      = $this->buildMockFuneralCompanyRepo();
        $this->mockJuristicPerson                          = $this->buildMockJuristicPerson();
        $mockJuristicPersonRepo                            = $this->buildMockJuristicPersonRepo();
        $mockSoleProprietorRepo                            = $this->buildMockSoleProprietorRepo();
        $mockBurialRepo                                    = $this->buildMockBurialRepo();
        $this->validator = new FuneralCompanyRepositoryValidator(
            $mockJuristicPersonRepo,
            $mockSoleProprietorRepo,
            $mockBurialRepo,
        );
    }

    public function testItSuccessfullyValidatesOrganizationIdUniqueness(): void
    {
        // Test it ignores the organization ID of the provided entity itself
        $this->assertNull(
            $this->validator->assertUnique($this->mockFuneralCompany, $this->mockFuneralCompanyRepo)
        );
        // Test it successfully validates another organization ID
        $this->assertNull(
            $this->validator->assertUnique($this->mockFuneralCompanyTotallyDifferent, $this->mockFuneralCompanyRepo)
        );
    }

    public function testItFailsWhenOrganizationIdAlreadyUsed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Похоронная фирма "%s" уже существует.',
            $this->mockJuristicPerson->name(),
        ));
        $this->validator->assertUnique($this->mockFuneralCompanyWithSameOrganizationId, $this->mockFuneralCompanyRepo);
    }

    public function testItSuccessfullyValidatesReferencesIntegrity(): void
    {
        $this->assertNull(
            $this->validator->assertReferencesNotBroken($this->mockFuneralCompany, $this->mockFuneralCompanyRepo)
        );
        $this->assertNull(
            $this->validator->assertReferencesNotBroken(
                $this->mockFuneralCompanyTotallyDifferent,
                $this->mockFuneralCompanyRepo,
            )
        );
    }

    public function testItFailsWhenOrganizationDoesNotExist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Организация с типом "%s" и ID "%s" не существует.',
            match ($this->invalidOrganizationId->idType()) {
                JuristicPerson::CLASS_SHORTCUT => JuristicPerson::CLASS_LABEL,
                SoleProprietor::CLASS_SHORTCUT => SoleProprietor::CLASS_LABEL,
            },
            $this->invalidOrganizationId->id()->value(),
        ));
        $this->validator->assertReferencesNotBroken(
            $this->mockFuneralCompanyWithInvalidOrganizationId,
            $this->mockFuneralCompanyRepo,
        );
    }

    public function testItSuccessfullyValidatesRemovability(): void
    {
        $this->assertNull(
            $this->validator->assertRemovable(
                $this->mockFuneralCompanyTotallyDifferent,
                $this->mockFuneralCompanyRepo,
            )
        );
    }

    public function testItFailsWhenRemovingIsNotAllowed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Похоронная фирма "%s" не может быть удалена, т.к. она указана для %d захоронений.',
            $this->mockJuristicPerson->name(),
            $this->relatedBurialCount,

        ));
        $this->validator->assertRemovable($this->mockFuneralCompany, $this->mockFuneralCompanyRepo);
    }

    private function buildMockFuneralCompany(): MockObject|FuneralCompany
    {
        $mockFuneralCompany = $this->createMock(FuneralCompany::class);
        $mockFuneralCompany->method('id')->willReturn($this->id);
        $mockFuneralCompany->method('organizationId')->willReturn($this->organizationId);

        return $mockFuneralCompany;
    }

    private function buildMockFuneralCompanyTotallyDifferent(): MockObject|FuneralCompany
    {
        $mockFuneralCompany = $this->createMock(FuneralCompany::class);
        $mockFuneralCompany->method('id')->willReturn(new FuneralCompanyId('FC002'));
        $mockFuneralCompany->method('organizationId')->willReturn($this->anotherOrganizationId);

        return $mockFuneralCompany;
    }

    private function buildMockFuneralCompanyWithSameOrganizationId(): MockObject|FuneralCompany
    {
        $mockFuneralCompany = $this->createMock(FuneralCompany::class);
        $mockFuneralCompany->method('id')->willReturn(new FuneralCompanyId('FC003'));
        $mockFuneralCompany->method('organizationId')->willReturn($this->organizationId);

        return $mockFuneralCompany;
    }

    private function buildMockFuneralCompanyWithInvalidOrganizationId(): MockObject|FuneralCompany
    {
        $mockFuneralCompany = $this->createMock(FuneralCompany::class);
        $mockFuneralCompany->method('id')->willReturn(new FuneralCompanyId('FC004'));
        $mockFuneralCompany->method('organizationId')->willReturn($this->invalidOrganizationId);

        return $mockFuneralCompany;
    }
    
    private function buildMockFuneralCompanyRepo(): MockObject|FuneralCompanyRepository
    {
        $mockFuneralCompanyRepo = $this->createMock(FuneralCompanyRepository::class);
        $mockFuneralCompanyRepo->method('doesSameOrganizationIdAlreadyUsed')->willReturnCallback(
            function (FuneralCompany $funeralCompany) {
                return match (true) {
                    $funeralCompany->id()->isEqual($this->id) => false,   // Ignore the entity itself
                    default =>
                        $funeralCompany->organizationId()->isEqual($this->organizationId),
                };
            }
        );

        return $mockFuneralCompanyRepo;
    }

    private function buildMockJuristicPerson(): MockObject|JuristicPerson
    {
        $mockJuristicPerson = $this->createMock(JuristicPerson::class);
        $mockJuristicPerson->method('id')->willReturn($this->organizationId->id());
        $mockJuristicPerson->method('name')->willReturn(new Name('ООО "Рога и копыта"'));

        return $mockJuristicPerson;
    }

    private function buildMockJuristicPersonRepo(): MockObject|JuristicPersonRepository
    {
        $mockJuristicPersonRepo = $this->createMock(JuristicPersonRepository::class);
        $mockJuristicPersonRepo->method('doesExistById')->willReturnCallback(
            function (JuristicPersonId $juristicPersonId) {
                return $juristicPersonId->isEqual($this->organizationId->id());
            }
        );
        $mockJuristicPersonRepo->method('findById')
            ->with($this->organizationId->id())
            ->willReturn($this->mockJuristicPerson);

        return $mockJuristicPersonRepo;
    }

    private function buildMockSoleProprietorRepo(): MockObject|SoleProprietorRepository
    {
        $mockSoleProprietorRepo = $this->createMock(SoleProprietorRepository::class);
        $mockSoleProprietorRepo->method('doesExistById')->willReturnCallback(
            function (SoleProprietorId $soleProprietorId) {
                return $soleProprietorId->isEqual($this->anotherOrganizationId->id());
            }
        );

        return $mockSoleProprietorRepo;
    }

    private function buildMockBurialRepo(): MockObject|BurialRepository
    {
        $mockBurialRepo = $this->createMock(BurialRepository::class);
        $mockBurialRepo->method('countByFuneralCompanyId')->willReturnCallback(
            function (FuneralCompanyId $funeralCompanyId) {
                return $funeralCompanyId->isEqual($this->id) ? $this->relatedBurialCount : 0;
            }
        );

        return $mockBurialRepo;
    }
}
