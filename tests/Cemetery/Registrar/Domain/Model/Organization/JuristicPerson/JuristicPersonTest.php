<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\Contact\Website;
use Cemetery\Registrar\Domain\Model\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\Inn;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\Kpp;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\Ogrn;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\Okpo;
use Cemetery\Registrar\Domain\Model\Organization\Name;
use Cemetery\Registrar\Domain\Model\Organization\Okved;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonTest extends AggregateRootTest
{
    private JuristicPerson $juristicPerson;

    public function setUp(): void
    {
        $juristicPersonId     = new JuristicPersonId('777');
        $juristicPersonName   = new Name('ООО "Рога и копыта"');
        $this->juristicPerson = new JuristicPerson($juristicPersonId, $juristicPersonName);
        $this->entity         = $this->juristicPerson;
    }

    public function testItHasValidClassShortcutConstant(): void
    {
        $this->assertSame('JURISTIC_PERSON', JuristicPerson::CLASS_SHORTCUT);
    }

    public function testItHasValidClassLabelConstant(): void
    {
        $this->assertSame('юрлицо', JuristicPerson::CLASS_LABEL);
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
    }

    public function testItSetsName(): void
    {
        $name = new Name('АО "Ромашка"');
        $this->juristicPerson->setName($name);
        $this->assertInstanceOf(Name::class, $this->juristicPerson->name());
        $this->assertTrue($this->juristicPerson->name()->isEqual($name));
    }

    public function testItSetsInn(): void
    {
        $inn = new Inn('7728168971');
        $this->juristicPerson->setInn($inn);
        $this->assertInstanceOf(Inn::class, $this->juristicPerson->inn());
        $this->assertTrue($this->juristicPerson->inn()->isEqual($inn));
    }

    public function testItSetsKpp(): void
    {
        $kpp = new Kpp('1234AB789');
        $this->juristicPerson->setKpp($kpp);
        $this->assertInstanceOf(Kpp::class, $this->juristicPerson->kpp());
        $this->assertTrue($this->juristicPerson->kpp()->isEqual($kpp));
    }

    public function testItSetsOgrn(): void
    {
        $ogrn = new Ogrn('1027700132195');
        $this->juristicPerson->setOgrn($ogrn);
        $this->assertInstanceOf(Ogrn::class, $this->juristicPerson->ogrn());
        $this->assertTrue($this->juristicPerson->ogrn()->isEqual($ogrn));
    }

    public function testItSetsOkpo(): void
    {
        $okpo = new Okpo('23584736');
        $this->juristicPerson->setOkpo($okpo);
        $this->assertInstanceOf(Okpo::class, $this->juristicPerson->okpo());
        $this->assertTrue($this->juristicPerson->okpo()->isEqual($okpo));
    }

    public function testItSetsOkved(): void
    {
        $okved = new Okved('74.82');
        $this->juristicPerson->setOkved($okved);
        $this->assertInstanceOf(Okved::class, $this->juristicPerson->okved());
        $this->assertTrue($this->juristicPerson->okved()->isEqual($okved));
    }

    public function testItSetsLegalAddress(): void
    {
        $legalAddress = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $this->juristicPerson->setLegalAddress($legalAddress);
        $this->assertInstanceOf(Address::class, $this->juristicPerson->legalAddress());
        $this->assertTrue($this->juristicPerson->legalAddress()->isEqual($legalAddress));
    }

    public function testItSetsPostalAddress(): void
    {
        $postalAddress = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $this->juristicPerson->setPostalAddress($postalAddress);
        $this->assertInstanceOf(Address::class, $this->juristicPerson->postalAddress());
        $this->assertTrue($this->juristicPerson->postalAddress()->isEqual($postalAddress));
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
        $this->assertTrue($this->juristicPerson->bankDetails()->isEqual($bankDetails));
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
        $this->assertTrue($this->juristicPerson->phoneAdditional()->isEqual($phoneAdditional));
    }

    public function testItSetsFax(): void
    {
        $fax = new PhoneNumber('+7-913-777-88-99');
        $this->juristicPerson->setFax($fax);
        $this->assertInstanceOf(PhoneNumber::class, $this->juristicPerson->fax());
        $this->assertTrue($this->juristicPerson->fax()->isEqual($fax));
    }

    public function testItSetsGeneralDirector(): void
    {
        $generalDirector = new FullName('Иванов Иван Иванович');
        $this->juristicPerson->setGeneralDirector($generalDirector);
        $this->assertInstanceOf(FullName::class, $this->juristicPerson->generalDirector());
        $this->assertTrue($this->juristicPerson->generalDirector()->isEqual($generalDirector));
    }

    public function testItSetsEmail(): void
    {
        $email = new Email('info@google.com');
        $this->juristicPerson->setEmail($email);
        $this->assertInstanceOf(Email::class, $this->juristicPerson->email());
        $this->assertTrue($this->juristicPerson->email()->isEqual($email));
    }

    public function testItSetsWebsite(): void
    {
        $website = new Website('https://example.com');
        $this->juristicPerson->setWebsite($website);
        $this->assertInstanceOf(Website::class, $this->juristicPerson->website());
        $this->assertTrue($this->juristicPerson->website()->isEqual($website));
    }
}
