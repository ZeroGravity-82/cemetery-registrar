<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Organization\OrganizationType;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyTest extends TestCase
{
    private FuneralCompany $funeralCompany;

    public function setUp(): void
    {
        $funeralCompanyId     = new FuneralCompanyId('777');
        $organizationType     = OrganizationType::juristicPerson();
        $organizationId       = new OrganizationId('888', $organizationType);
        $this->funeralCompany = new FuneralCompany($funeralCompanyId, $organizationId);
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(FuneralCompanyId::class, $this->funeralCompany->getId());
        $this->assertSame('777', (string) $this->funeralCompany->getId());
        $this->assertInstanceOf(OrganizationId::class, $this->funeralCompany->getOrganizationId());
        $this->assertSame('888', $this->funeralCompany->getOrganizationId()->getValue());
        $this->assertSame(OrganizationType::JURISTIC_PERSON, (string) $this->funeralCompany->getOrganizationId()->getType());
        $this->assertNull($this->funeralCompany->getNote());
    }

    public function testItSetsNote(): void
    {
        $note = 'Некоторый комментарий';
        $this->funeralCompany->setNote($note);
        $this->assertSame('Некоторый комментарий', $this->funeralCompany->getNote());
    }
}
