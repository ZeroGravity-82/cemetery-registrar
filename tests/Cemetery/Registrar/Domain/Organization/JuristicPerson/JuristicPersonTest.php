<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\Inn;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\Kpp;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\Ogrn;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\Okpo;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\Okved;
use Cemetery\Tests\Registrar\Domain\AbstractAggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonTest extends AbstractAggregateRootTest
{
    private JuristicPerson $juristicPerson;

    public function setUp(): void
    {
        $juristicPersonId     = new JuristicPersonId('777');
        $juristicPersonName   = new Name('ООО "Рога и копыта"');
        $this->juristicPerson = new JuristicPerson($juristicPersonId, $juristicPersonName);
        $this->entity         = $this->juristicPerson;
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(JuristicPersonId::class, $this->juristicPerson->id());
        $this->assertSame('777', (string) $this->juristicPerson->id());
        $this->assertInstanceOf(Name::class, $this->juristicPerson->name());
        $this->assertSame('ООО "Рога и копыта"', (string) $this->juristicPerson->name());
        $this->assertNull($this->juristicPerson->inn());
        $this->assertNull($this->juristicPerson->kpp());
        $this->assertNull($this->juristicPerson->ogrn());
        $this->assertNull($this->juristicPerson->okpo());
        $this->assertNull($this->juristicPerson->okved());
        $this->assertNull($this->juristicPerson->legalAddress());
        $this->assertNull($this->juristicPerson->postalAddress());
        $this->assertNull($this->juristicPerson->bankDetails());
        $this->assertNull($this->juristicPerson->phone());
        $this->assertNull($this->juristicPerson->phoneAdditional());
        $this->assertNull($this->juristicPerson->fax());
        $this->assertNull($this->juristicPerson->generalDirector());
        $this->assertNull($this->juristicPerson->email());
        $this->assertNull($this->juristicPerson->website());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->juristicPerson->createdAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->juristicPerson->createdAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->juristicPerson->updatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->juristicPerson->updatedAt());
    }

    public function testItSetsInn(): void
    {
        $inn = new Inn('7728168971');
        $this->juristicPerson->setInn($inn);
        $this->assertInstanceOf(Inn::class, $this->juristicPerson->inn());
        $this->assertSame('7728168971', (string) $this->juristicPerson->inn());
    }

    public function testItSetsKpp(): void
    {
        $kpp = new Kpp('1234AB789');
        $this->juristicPerson->setKpp($kpp);
        $this->assertInstanceOf(Kpp::class, $this->juristicPerson->kpp());
        $this->assertSame('1234AB789', (string) $this->juristicPerson->kpp());
    }

    public function testItSetsOgrn(): void
    {
        $ogrn = new Ogrn('1027700132195');
        $this->juristicPerson->setOgrn($ogrn);
        $this->assertInstanceOf(Ogrn::class, $this->juristicPerson->ogrn());
        $this->assertSame('1027700132195', (string) $this->juristicPerson->ogrn());
    }

    public function testItSetsOkpo(): void
    {
        $okpo = new Okpo('23584736');
        $this->juristicPerson->setOkpo($okpo);
        $this->assertInstanceOf(Okpo::class, $this->juristicPerson->okpo());
        $this->assertSame('23584736', (string) $this->juristicPerson->okpo());
    }

    public function testItSetsOkved(): void
    {
        $okved = new Okved('74.82');
        $this->juristicPerson->setOkved($okved);
        $this->assertInstanceOf(Okved::class, $this->juristicPerson->okved());
        $this->assertSame('74.82', (string) $this->juristicPerson->okved());
    }

    public function testItSetsLegalAddress(): void
    {
        $legalAddress = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $this->juristicPerson->setLegalAddress($legalAddress);
        $this->assertInstanceOf(Address::class, $this->juristicPerson->legalAddress());
        $this->assertSame(
            'г. Новосибирск, ул. 3 Интернационала, д. 127',
            (string) $this->juristicPerson->legalAddress()
        );
    }

    public function testItSetsPostalAddress(): void
    {
        $postalAddress = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $this->juristicPerson->setPostalAddress($postalAddress);
        $this->assertInstanceOf(Address::class, $this->juristicPerson->postalAddress());
        $this->assertSame(
            'г. Новосибирск, ул. 3 Интернационала, д. 127',
            (string) $this->juristicPerson->postalAddress()
        );
    }

    public function testItSetsBankDetails(): void
    {
        $bankDetails = new BankDetails(
            'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"',
            '045004816',
            '30101810500000000816',
            '40702810904000040651',
        );
        $this->juristicPerson->setBankDetails($bankDetails);
        $this->assertInstanceOf(BankDetails::class, $this->juristicPerson->bankDetails());
        $this->assertSame(
            'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"',
            (string) $this->juristicPerson->bankDetails()->bankName()
        );
        $this->assertSame('045004816', (string) $this->juristicPerson->bankDetails()->bik());
        $this->assertSame('30101810500000000816', (string) $this->juristicPerson->bankDetails()->correspondentAccount());
        $this->assertSame('40702810904000040651', (string) $this->juristicPerson->bankDetails()->currentAccount());
    }

    public function testItSetsPhone(): void
    {
        $phone = new PhoneNumber('+7-913-777-88-99');
        $this->juristicPerson->setPhone($phone);
        $this->assertInstanceOf(PhoneNumber::class, $this->juristicPerson->phone());
        $this->assertSame('+7-913-777-88-99', (string) $this->juristicPerson->phone());
    }

    public function testItSetsPhoneAdditional(): void
    {
        $phoneAdditional = new PhoneNumber('+7-913-777-88-99');
        $this->juristicPerson->setPhoneAdditional($phoneAdditional);
        $this->assertInstanceOf(PhoneNumber::class, $this->juristicPerson->phoneAdditional());
        $this->assertSame('+7-913-777-88-99', (string) $this->juristicPerson->phoneAdditional());
    }

    public function testItSetsFax(): void
    {
        $fax = new PhoneNumber('+7-913-777-88-99');
        $this->juristicPerson->setFax($fax);
        $this->assertInstanceOf(PhoneNumber::class, $this->juristicPerson->fax());
        $this->assertSame('+7-913-777-88-99', (string) $this->juristicPerson->fax());
    }

    public function testItSetsGeneralDirector(): void
    {
        $generalDirector = new FullName('Иванов Иван Иванович');
        $this->juristicPerson->setGeneralDirector($generalDirector);
        $this->assertInstanceOf(FullName::class, $this->juristicPerson->generalDirector());
        $this->assertSame('Иванов Иван Иванович', (string) $this->juristicPerson->generalDirector());
    }

    public function testItSetsEmail(): void
    {
        $email = new Email('info@google.com');
        $this->juristicPerson->setEmail($email);
        $this->assertInstanceOf(Email::class, $this->juristicPerson->email());
        $this->assertSame('info@google.com', (string) $this->juristicPerson->email());
    }

    public function testItSetsWebsite(): void
    {
        $website = new Website('https://example.com');
        $this->juristicPerson->setWebsite($website);
        $this->assertInstanceOf(Website::class, $this->juristicPerson->website());
        $this->assertSame('https://example.com', (string) $this->juristicPerson->website());
    }
}
