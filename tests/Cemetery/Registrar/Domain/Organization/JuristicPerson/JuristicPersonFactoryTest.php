<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonFactory;
use Cemetery\Tests\Registrar\Domain\EntityFactoryTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonFactoryTest extends EntityFactoryTest
{
    private JuristicPersonFactory $juristicPersonFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->juristicPersonFactory = new JuristicPersonFactory($this->mockIdentityGenerator);
    }

    public function testItCreatesJuristicPerson(): void
    {
        $name                            = 'ООО "Рога и копыта"';
        $inn                             = '7728168971';
        $kpp                             = '1234AB789';
        $ogrn                            = '1027700132195';
        $okpo                            = '23584736';
        $okved                           = '74.82';
        $legalAddress                    = 'г. Новосибирск, ул. 3 Интернационала, д. 127';
        $postalAddress                   = 'г. Москва, ул. Стандартная, д. 21, корп. 1, кв. 5';
        $bankDetailsBankName             = 'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"';
        $bankDetailsBik                  = '045004816';
        $bankDetailsCorrespondentAccount = '30101810500000000816';
        $bankDetailsCurrentAccount       = '40702810904000040651';
        $phone                           = '+7-913-777-88-99';
        $phoneAdditional                 = '8(383)123-45-67';
        $fax                             = '8(383)123-46-89';
        $generalDirector                 = 'Иванов Иван Иванович';
        $email                           = 'info@google.com';
        $website                         = 'https://example.com';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $juristicPerson = $this->juristicPersonFactory->create(
            $name,
            $inn,
            $kpp,
            $ogrn,
            $okpo,
            $okved,
            $legalAddress,
            $postalAddress,
            $bankDetailsBankName,
            $bankDetailsBik,
            $bankDetailsCorrespondentAccount,
            $bankDetailsCurrentAccount,
            $phone,
            $phoneAdditional,
            $fax,
            $generalDirector,
            $email,
            $website,
        );
        $this->assertInstanceOf(JuristicPerson::class, $juristicPerson);
        $this->assertSame(self::ENTITY_ID, $juristicPerson->id()->value());
        $this->assertSame($name, $juristicPerson->name()->value());
        $this->assertSame($inn, $juristicPerson->inn()->value());
        $this->assertSame($kpp, $juristicPerson->kpp()->value());
        $this->assertSame($ogrn, $juristicPerson->ogrn()->value());
        $this->assertSame($okpo, $juristicPerson->okpo()->value());
        $this->assertSame($okved, $juristicPerson->okved()->value());
        $this->assertSame($legalAddress, $juristicPerson->legalAddress()->value());
        $this->assertSame($postalAddress, $juristicPerson->postalAddress()->value());
        $this->assertSame($bankDetailsBankName, $juristicPerson->bankDetails()->bankName()->value());
        $this->assertSame($bankDetailsBik, $juristicPerson->bankDetails()->bik()->value());
        $this->assertSame($bankDetailsCorrespondentAccount, $juristicPerson->bankDetails()->correspondentAccount()->value());
        $this->assertSame($bankDetailsCurrentAccount, $juristicPerson->bankDetails()->currentAccount()->value());
        $this->assertSame($phone, $juristicPerson->phone()->value());
        $this->assertSame($phoneAdditional, $juristicPerson->phoneAdditional()->value());
        $this->assertSame($fax, $juristicPerson->fax()->value());
        $this->assertSame($generalDirector, $juristicPerson->generalDirector()->value());
        $this->assertSame($email, $juristicPerson->email()->value());
        $this->assertSame($website, $juristicPerson->website()->value());
    }

    public function testItCreatesJuristicPersonWithoutOptionalFields(): void
    {
        $name = 'ООО "Рога и копыта"';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $juristicPerson = $this->juristicPersonFactory->create(
            $name,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        );
        $this->assertInstanceOf(JuristicPerson::class, $juristicPerson);
        $this->assertSame(self::ENTITY_ID, $juristicPerson->id()->value());
        $this->assertSame($name, $juristicPerson->name()->value());
        $this->assertNull($juristicPerson->inn());
        $this->assertNull($juristicPerson->kpp());
        $this->assertNull($juristicPerson->ogrn());
        $this->assertNull($juristicPerson->okpo());
        $this->assertNull($juristicPerson->okved());
        $this->assertNull($juristicPerson->legalAddress());
        $this->assertNull($juristicPerson->postalAddress());
        $this->assertNull($juristicPerson->bankDetails());
        $this->assertNull($juristicPerson->phone());
        $this->assertNull($juristicPerson->phoneAdditional());
        $this->assertNull($juristicPerson->fax());
        $this->assertNull($juristicPerson->generalDirector());
        $this->assertNull($juristicPerson->email());
        $this->assertNull($juristicPerson->website());
    }
}
