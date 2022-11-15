<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\FuneralCompany;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyFactory;
use Cemetery\Tests\Registrar\Domain\Model\AbstractEntityFactoryTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyFactoryTest extends AbstractEntityFactoryTest
{
    private FuneralCompanyFactory $funeralCompanyFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->funeralCompanyFactory = new FuneralCompanyFactory($this->mockIdentityGenerator);
    }

    public function testItCreatesFuneralCompany(): void
    {
        $name = 'Апостол';
        $note = 'Примечание 1';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $funeralCompany = $this->funeralCompanyFactory->create(
            $name,
            $note,
        );
        $this->assertInstanceOf(FuneralCompany::class, $funeralCompany);
        $this->assertSame(self::ENTITY_ID, $funeralCompany->id()->value());
        $this->assertSame($name, $funeralCompany->name()->value());
        $this->assertSame($note, $funeralCompany->note()->value());
    }

    public function testItCreatesFuneralCompanyWithoutOptionalFields(): void
    {
        $name = 'Апостол';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $funeralCompany = $this->funeralCompanyFactory->create(
            $name,
            null,
        );
        $this->assertInstanceOf(FuneralCompany::class, $funeralCompany);
        $this->assertSame(self::ENTITY_ID, $funeralCompany->id()->value());
        $this->assertSame($name, $funeralCompany->name()->value());
        $this->assertNull($funeralCompany->note());
    }
}
