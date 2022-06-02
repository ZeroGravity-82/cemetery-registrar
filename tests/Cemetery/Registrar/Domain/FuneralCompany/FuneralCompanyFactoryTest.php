<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyFactory;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Tests\Registrar\Domain\EntityFactoryTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyFactoryTest extends EntityFactoryTest
{
    private FuneralCompanyFactory $funeralCompanyFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->funeralCompanyFactory = new FuneralCompanyFactory($this->mockIdentityGenerator);
    }

    public function testItCreatesFuneralCompany(): void
    {
        $organizationId   = 'JP001';
        $organizationType = JuristicPerson::CLASS_SHORTCUT;
        $note             = 'Примечание 1';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $funeralCompany = $this->funeralCompanyFactory->create(
            $organizationId,
            $organizationType,
            $note,
        );
        $this->assertInstanceOf(FuneralCompany::class, $funeralCompany);
        $this->assertSame(self::ENTITY_ID, $funeralCompany->id()->value());
        $this->assertSame($organizationId, $funeralCompany->organizationId()->id()->value());
        $this->assertSame($organizationType, $funeralCompany->organizationId()->idClassShortcut());
        $this->assertSame($note, $funeralCompany->note()->value());
    }

    public function testItCreatesFuneralCompanyWithoutOptionalFields(): void
    {
        $organizationId   = 'JP001';
        $organizationType = JuristicPerson::CLASS_SHORTCUT;
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $funeralCompany = $this->funeralCompanyFactory->create(
            $organizationId,
            $organizationType,
            null,
        );
        $this->assertInstanceOf(FuneralCompany::class, $funeralCompany);
        $this->assertSame(self::ENTITY_ID, $funeralCompany->id()->value());
        $this->assertSame($organizationId, $funeralCompany->organizationId()->id()->value());
        $this->assertSame($organizationType, $funeralCompany->organizationId()->idClassShortcut());
        $this->assertNull($funeralCompany->note());
    }
}
