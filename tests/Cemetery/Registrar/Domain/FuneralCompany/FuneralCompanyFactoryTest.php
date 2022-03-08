<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyFactory;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\IdentityGeneratorInterface;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonFactory;
use Cemetery\Registrar\Domain\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Organization\OrganizationType;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyFactoryTest extends TestCase
{
    private FuneralCompanyFactory     $funeralCompanyFactory;
    private MockObject|FuneralCompany $mockFuneralCompany;

    public function setUp(): void
    {
        $mockIdentityGenerator       = $this->buildMockIdentityGenerator();
        $mockJuristicPersonFactory   = $this->buildMockJuristicPersonFactory();
        $mockSoleProprietorFactory   = $this->buildMockSoleProprietorFactory();
        $this->funeralCompanyFactory = new FuneralCompanyFactory(
            $mockIdentityGenerator,
            $mockJuristicPersonFactory,
            $mockSoleProprietorFactory,
        );
        $this->mockFuneralCompany = $this->createMock(FuneralCompany::class);
    }

    public function testItCreatesAFuneralCompany(): void
    {
        $funeralCompany = $this->funeralCompanyFactory->create('O111', OrganizationType::JURISTIC_PERSON);
        $this->assertInstanceOf(FuneralCompany::class, $funeralCompany);
        $this->assertInstanceOf(FuneralCompanyId::class, $funeralCompany->getId());
        $this->assertSame('555', (string) $funeralCompany->getId());
        $this->assertInstanceOf(OrganizationId::class, $funeralCompany->getOrganizationId());
        $this->assertSame('O111', $funeralCompany->getOrganizationId()->getValue());
        $this->assertSame(OrganizationType::JURISTIC_PERSON, (string) $funeralCompany->getOrganizationId()->getType());
    }

    public function testItFailsToCreateAFuneralCompanyWithoutOrganizationIdValue(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('ФИО не указано.');
        $this->funeralCompanyFactory->createForDeceased(null, null);
    }

    public function testItFailsToCreateAFuneralCompanyWithoutOrganizationIdType(): void
    {

    }

    private function buildMockIdentityGenerator(): MockObject|IdentityGeneratorInterface
    {
        $mockIdentityGenerator = $this->createMock(IdentityGeneratorInterface::class);
        $mockIdentityGenerator->method('getNextIdentity')->willReturn('555');

        return $mockIdentityGenerator;
    }

    private function buildMockJuristicPersonFactory(): MockObject|JuristicPersonFactory
    {
        $mockJuristicPersonFactory = $this->createMock(JuristicPersonFactory::class);

        return $mockJuristicPersonFactory;
    }

    private function buildMockSoleProprietorFactory(): MockObject|SoleProprietorFactory
    {
        $mockSoleProprietorFactory = $this->createMock(SoleProprietorFactory::class);

        return $mockSoleProprietorFactory;
    }
}
