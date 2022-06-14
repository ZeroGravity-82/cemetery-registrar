<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\FuneralCompany;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyNote;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyTest extends AggregateRootTest
{
    private FuneralCompanyId $funeralCompanyIdA;
    private OrganizationId   $organizationIdA;
    private FuneralCompany   $funeralCompanyA;
    
    public function setUp(): void
    {
        $this->funeralCompanyIdA = new FuneralCompanyId('FC001');
        $this->organizationIdA   = new OrganizationId(new JuristicPersonId('JP001'));
        $this->funeralCompanyA   = new FuneralCompany($this->funeralCompanyIdA, $this->organizationIdA);
        $this->entity            = $this->funeralCompanyA;
    }

    public function testItSuccessfullyCreatedForJuristicPerson(): void
    {
        $this->assertInstanceOf(FuneralCompanyId::class, $this->funeralCompanyA->id());
        $this->assertTrue($this->funeralCompanyA->id()->isEqual($this->funeralCompanyIdA));
        $this->assertInstanceOf(OrganizationId::class, $this->funeralCompanyA->organizationId());
        $this->assertTrue($this->funeralCompanyA->organizationId()->isEqual($this->organizationIdA));
        $this->assertNull($this->funeralCompanyA->note());
    }

    public function testItSuccessfullyCreatedForSoleProprietor(): void
    {
        $funeralCompanyIdB = new FuneralCompanyId('FC002');
        $organizationIdB   = new OrganizationId(new SoleProprietorId('SP001'));
        $funeralCompanyB   = new FuneralCompany($funeralCompanyIdB, $organizationIdB);
        $this->assertTrue($funeralCompanyB->id()->isEqual($funeralCompanyIdB));
        $this->assertInstanceOf(OrganizationId::class, $funeralCompanyB->organizationId());
        $this->assertTrue($funeralCompanyB->organizationId()->isEqual($organizationIdB));
        $this->assertNull($funeralCompanyB->note());
    }

    public function testItSetsNote(): void
    {
        $note = new FuneralCompanyNote('Примечание 1');
        $this->funeralCompanyA->setNote($note);
        $this->assertInstanceOf(FuneralCompanyNote::class, $this->funeralCompanyA->note());
        $this->assertTrue($this->funeralCompanyA->note()->isEqual($note));

        $this->funeralCompanyA->setNote(null);
        $this->assertNull($this->funeralCompanyA->note());
    }
}
