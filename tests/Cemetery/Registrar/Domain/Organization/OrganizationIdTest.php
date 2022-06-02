<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OrganizationIdTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $organizationIdId = new OrganizationId(new JuristicPersonId('JP001'));
        $this->assertInstanceOf(JuristicPersonId::class, $organizationIdId->id());
        $this->assertSame('JP001', $organizationIdId->id()->value());

        $organizationIdId = new OrganizationId(new SoleProprietorId('SP001'));
        $this->assertInstanceOf(SoleProprietorId::class, $organizationIdId->id());
        $this->assertSame('SP001', $organizationIdId->id()->value());
    }

    public function testItComparable(): void
    {
        $organizationIdIdA = new OrganizationId(new SoleProprietorId('ID001'));
        $organizationIdIdB = new OrganizationId(new JuristicPersonId('ID001'));
        $organizationIdIdC = new OrganizationId(new SoleProprietorId('ID002'));
        $organizationIdIdD = new OrganizationId(new SoleProprietorId('ID001'));

        $this->assertFalse($organizationIdIdA->isEqual($organizationIdIdB));
        $this->assertFalse($organizationIdIdA->isEqual($organizationIdIdC));
        $this->assertTrue($organizationIdIdA->isEqual($organizationIdIdD));
        $this->assertFalse($organizationIdIdB->isEqual($organizationIdIdC));
        $this->assertFalse($organizationIdIdB->isEqual($organizationIdIdD));
        $this->assertFalse($organizationIdIdC->isEqual($organizationIdIdD));
    }
}
