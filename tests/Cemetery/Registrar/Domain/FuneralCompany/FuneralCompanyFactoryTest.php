<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyFactory;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\IdentityGeneratorInterface;
use Cemetery\Registrar\Domain\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Organization\OrganizationType;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyFactoryTest extends TestCase
{
    private FuneralCompanyFactory $funeralCompanyFactory;
    private OrganizationId        $organizationId;

    public function setUp(): void
    {
        $mockIdentityGenerator = $this->createMock(IdentityGeneratorInterface::class);
        $mockIdentityGenerator->method('getNextIdentity')->willReturn('FC001');
        $this->funeralCompanyFactory = new FuneralCompanyFactory($mockIdentityGenerator);

        $this->organizationId = new OrganizationId('O001', OrganizationType::juristicPerson());
    }

    public function testItCreatesAFuneralCompany(): void
    {
        $funeralCompany = $this->funeralCompanyFactory->create($this->organizationId, 'Некоторый комментарий');
        $this->assertInstanceOf(FuneralCompany::class, $funeralCompany);
        $this->assertInstanceOf(FuneralCompanyId::class, $funeralCompany->getId());
        $this->assertSame('FC001', (string) $funeralCompany->getId());
        $this->assertInstanceOf(OrganizationId::class, $funeralCompany->getOrganizationId());
        $this->assertSame('O001', $funeralCompany->getOrganizationId()->getValue());
        $this->assertSame(OrganizationType::JURISTIC_PERSON, (string) $funeralCompany->getOrganizationId()->getType());
        $this->assertSame('Некоторый комментарий', $funeralCompany->getNote());
    }

    public function testItCreatesAFuneralCompanyWithoutOptionalFields(): void
    {
        $funeralCompany = $this->funeralCompanyFactory->create($this->organizationId, null);
        $this->assertInstanceOf(FuneralCompany::class, $funeralCompany);
        $this->assertNull($funeralCompany->getNote());
    }
}
