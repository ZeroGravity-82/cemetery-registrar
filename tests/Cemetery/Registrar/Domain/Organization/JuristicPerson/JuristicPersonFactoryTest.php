<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonBuilder;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonFactoryTest extends TestCase
{
    private MockObject|JuristicPersonBuilder $mockJuristicPersonBuilder;
    private JuristicPersonFactory            $juristicPersonFactory;
    private MockObject|JuristicPerson        $mockJuristicPerson;

    public function setUp(): void
    {
        $this->mockJuristicPersonBuilder = $this->createMock(JuristicPersonBuilder::class);
        $this->juristicPersonFactory     = new JuristicPersonFactory($this->mockJuristicPersonBuilder);
        $this->mockJuristicPerson        = $this->createMock(JuristicPerson::class);
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
        $this->mockJuristicPersonBuilder->expects($this->once())->method('initialize')->with($name);
        $this->mockJuristicPersonBuilder->expects($this->once())->method('addInn')->with($inn);
        $this->mockJuristicPersonBuilder->expects($this->once())->method('addKpp')->with($kpp);
        $this->mockJuristicPersonBuilder->expects($this->once())->method('addOgrn')->with($ogrn);
        $this->mockJuristicPersonBuilder->expects($this->once())->method('addOkpo')->with($okpo);
        $this->mockJuristicPersonBuilder->expects($this->once())->method('addOkved')->with($okved);
        $this->mockJuristicPersonBuilder->expects($this->once())->method('addLegalAddress')->with($legalAddress);
        $this->mockJuristicPersonBuilder->expects($this->once())->method('addPostalAddress')->with($postalAddress);
        $this->mockJuristicPersonBuilder->expects($this->once())->method('addBankDetails')->with(
            $bankDetailsBankName,
            $bankDetailsBik,
            $bankDetailsCorrespondentAccount,
            $bankDetailsCurrentAccount,
        );
        $this->mockJuristicPersonBuilder->expects($this->once())->method('addPhone')->with($phone);
        $this->mockJuristicPersonBuilder->expects($this->once())->method('addPhoneAdditional')->with($phoneAdditional);
        $this->mockJuristicPersonBuilder->expects($this->once())->method('addFax')->with($fax);
        $this->mockJuristicPersonBuilder->expects($this->once())->method('addGeneralDirector')->with($generalDirector);
        $this->mockJuristicPersonBuilder->expects($this->once())->method('addEmail')->with($email);
        $this->mockJuristicPersonBuilder->expects($this->once())->method('addWebsite')->with($website);
        $this->mockJuristicPersonBuilder->expects($this->once())->method('build')->willReturn($this->mockJuristicPerson);
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
    }

    public function testItCreatesJuristicPersonWithoutOptionalFields(): void
    {
        $name = 'ООО "Рога и копыта"';
        $this->mockJuristicPersonBuilder->expects($this->once())->method('initialize')->with($name);
        $this->mockJuristicPersonBuilder->expects($this->never())->method('addInn');
        $this->mockJuristicPersonBuilder->expects($this->never())->method('addKpp');
        $this->mockJuristicPersonBuilder->expects($this->never())->method('addOgrn');
        $this->mockJuristicPersonBuilder->expects($this->never())->method('addOkpo');
        $this->mockJuristicPersonBuilder->expects($this->never())->method('addOkved');
        $this->mockJuristicPersonBuilder->expects($this->never())->method('addLegalAddress');
        $this->mockJuristicPersonBuilder->expects($this->never())->method('addPostalAddress');
        $this->mockJuristicPersonBuilder->expects($this->never())->method('addBankDetails');
        $this->mockJuristicPersonBuilder->expects($this->never())->method('addPhone');
        $this->mockJuristicPersonBuilder->expects($this->never())->method('addPhoneAdditional');
        $this->mockJuristicPersonBuilder->expects($this->never())->method('addFax');
        $this->mockJuristicPersonBuilder->expects($this->never())->method('addGeneralDirector');
        $this->mockJuristicPersonBuilder->expects($this->never())->method('addEmail');
        $this->mockJuristicPersonBuilder->expects($this->never())->method('addWebsite');
        $this->mockJuristicPersonBuilder->expects($this->once())->method('build')->willReturn($this->mockJuristicPerson);
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

    public function testItFailsToCreateJuristicPersonWithoutName(): void
    {
        $this->expectExceptionForNotProvidedName();
        $this->juristicPersonFactory->create(
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
            null,
        );
    }

    private function expectExceptionForNotProvidedName(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Наименование не указано.');
    }
}
