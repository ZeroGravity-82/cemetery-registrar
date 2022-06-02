<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OrganizationIdFactoryTest extends TestCase
{
    private OrganizationIdFactory $organizationIdFactory;

    public function setUp(): void
    {
        $this->organizationIdFactory = new OrganizationIdFactory();
    }

    public function testItCreatesOrganizationId(): void
    {
        $organizationId = $this->organizationIdFactory->create(new SoleProprietorId('ID001'));
        $this->assertInstanceOf(OrganizationId::class, $organizationId);
        $this->assertInstanceOf(SoleProprietorId::class, $organizationId->id());
        $this->assertSame('ID001', $organizationId->id()->value());

        $organizationId = $this->organizationIdFactory->create(new JuristicPersonId('ID002'));
        $this->assertInstanceOf(OrganizationId::class, $organizationId);
        $this->assertInstanceOf(JuristicPersonId::class, $organizationId->id());
        $this->assertSame('ID002', $organizationId->id()->value());
    }

    public function testItCreatesOrganizationIdForSoleProprietor(): void
    {
        $organizationId = $this->organizationIdFactory->createForSoleProprietor('ID003');
        $this->assertInstanceOf(OrganizationId::class, $organizationId);
        $this->assertInstanceOf(SoleProprietorId::class, $organizationId->id());
        $this->assertSame('ID003', $organizationId->id()->value());
    }

    public function testItCreatesOrganizationIdForJuristicPerson(): void
    {
        $organizationId = $this->organizationIdFactory->createForJuristicPerson('ID004');
        $this->assertInstanceOf(OrganizationId::class, $organizationId);
        $this->assertInstanceOf(JuristicPersonId::class, $organizationId->id());
        $this->assertSame('ID004', $organizationId->id()->value());
    }
}
