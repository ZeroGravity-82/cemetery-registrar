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
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonTest extends TestCase
{
    private JuristicPerson $juristicPerson;

    public function setUp(): void
    {
        $juristicPersonId     = new JuristicPersonId('777');
        $juristicPersonName   = new Name('ООО "Рога и копыта"');
        $this->juristicPerson = new JuristicPerson($juristicPersonId, $juristicPersonName);
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(JuristicPersonId::class, $this->juristicPerson->getId());
        $this->assertSame('777', (string) $this->juristicPerson->getId());
        $this->assertInstanceOf(Name::class, $this->juristicPerson->getName());
        $this->assertSame('ООО "Рога и копыта"', (string) $this->juristicPerson->getName());
        $this->assertNull($this->juristicPerson->getInn());
        $this->assertNull($this->juristicPerson->getKpp());
        $this->assertNull($this->juristicPerson->getOgrn());
        $this->assertNull($this->juristicPerson->getOkpo());
        $this->assertNull($this->juristicPerson->getOkved());
        $this->assertNull($this->juristicPerson->getLegalAddress());
        $this->assertNull($this->juristicPerson->getPostalAddress());
        $this->assertNull($this->juristicPerson->getBankDetails());
        $this->assertNull($this->juristicPerson->getPhone());
        $this->assertNull($this->juristicPerson->getPhoneAdditional());
        $this->assertNull($this->juristicPerson->getFax());
        $this->assertNull($this->juristicPerson->getGeneralDirector());
        $this->assertNull($this->juristicPerson->getEmail());
        $this->assertNull($this->juristicPerson->getWebsite());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->juristicPerson->getCreatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->juristicPerson->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->juristicPerson->getUpdatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->juristicPerson->getUpdatedAt());
    }

    public function testItSetsInn(): void
    {
        $inn = new Inn('7728168971');
        $this->juristicPerson->setInn($inn);
        $this->assertInstanceOf(Inn::class, $this->juristicPerson->getInn());
        $this->assertSame('7728168971', (string) $this->juristicPerson->getInn());
    }

    public function testItSetsKpp(): void
    {
        $kpp = new Kpp('1234AB789');
        $this->juristicPerson->setKpp($kpp);
        $this->assertInstanceOf(Kpp::class, $this->juristicPerson->getKpp());
        $this->assertSame('1234AB789', (string) $this->juristicPerson->getKpp());
    }

    public function testItSetsOgrn(): void
    {
        $ogrn = new Ogrn('1027700132195');
        $this->juristicPerson->setOgrn($ogrn);
        $this->assertInstanceOf(Ogrn::class, $this->juristicPerson->getOgrn());
        $this->assertSame('1027700132195', (string) $this->juristicPerson->getOgrn());
    }

    public function testItSetsOkpo(): void
    {
        $okpo = new Okpo('23584736');
        $this->juristicPerson->setOkpo($okpo);
        $this->assertInstanceOf(Okpo::class, $this->juristicPerson->getOkpo());
        $this->assertSame('23584736', (string) $this->juristicPerson->getOkpo());
    }

    public function testItSetsOkved(): void
    {
        $okved = new Okved('74.82');
        $this->juristicPerson->setOkved($okved);
        $this->assertInstanceOf(Okved::class, $this->juristicPerson->getOkved());
        $this->assertSame('74.82', (string) $this->juristicPerson->getOkved());
    }

    public function testItSetsLegalAddress(): void
    {
        $legalAddress = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $this->juristicPerson->setLegalAddress($legalAddress);
        $this->assertInstanceOf(Address::class, $this->juristicPerson->getLegalAddress());
        $this->assertSame(
            'г. Новосибирск, ул. 3 Интернационала, д. 127',
            (string) $this->juristicPerson->getLegalAddress()
        );
    }

    public function testItSetsPostalAddress(): void
    {
        $postalAddress = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $this->juristicPerson->setPostalAddress($postalAddress);
        $this->assertInstanceOf(Address::class, $this->juristicPerson->getPostalAddress());
        $this->assertSame(
            'г. Новосибирск, ул. 3 Интернационала, д. 127',
            (string) $this->juristicPerson->getPostalAddress()
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
        $this->assertInstanceOf(BankDetails::class, $this->juristicPerson->getBankDetails());
        $this->assertSame(
            'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"',
            (string) $this->juristicPerson->getBankDetails()->getBankName()
        );
        $this->assertSame('045004816', (string) $this->juristicPerson->getBankDetails()->getBik());
        $this->assertSame('30101810500000000816', (string) $this->juristicPerson->getBankDetails()->getCorrespondentAccount());
        $this->assertSame('40702810904000040651', (string) $this->juristicPerson->getBankDetails()->getCurrentAccount());
    }

    public function testItSetsPhone(): void
    {
        $phone = new PhoneNumber('+7-913-777-88-99');
        $this->juristicPerson->setPhone($phone);
        $this->assertInstanceOf(PhoneNumber::class, $this->juristicPerson->getPhone());
        $this->assertSame('+7-913-777-88-99', (string) $this->juristicPerson->getPhone());
    }

    public function testItSetsPhoneAdditional(): void
    {
        $phoneAdditional = new PhoneNumber('+7-913-777-88-99');
        $this->juristicPerson->setPhoneAdditional($phoneAdditional);
        $this->assertInstanceOf(PhoneNumber::class, $this->juristicPerson->getPhoneAdditional());
        $this->assertSame('+7-913-777-88-99', (string) $this->juristicPerson->getPhoneAdditional());
    }

    public function testItSetsFax(): void
    {
        $fax = new PhoneNumber('+7-913-777-88-99');
        $this->juristicPerson->setFax($fax);
        $this->assertInstanceOf(PhoneNumber::class, $this->juristicPerson->getFax());
        $this->assertSame('+7-913-777-88-99', (string) $this->juristicPerson->getFax());
    }

    public function testItSetsGeneralDirector(): void
    {
        $generalDirector = new FullName('Иванов Иван Иванович');
        $this->juristicPerson->setGeneralDirector($generalDirector);
        $this->assertInstanceOf(FullName::class, $this->juristicPerson->getGeneralDirector());
        $this->assertSame('Иванов Иван Иванович', (string) $this->juristicPerson->getGeneralDirector());
    }

    public function testItSetsEmail(): void
    {
        $email = new Email('info@google.com');
        $this->juristicPerson->setEmail($email);
        $this->assertInstanceOf(Email::class, $this->juristicPerson->getEmail());
        $this->assertSame('info@google.com', (string) $this->juristicPerson->getEmail());
    }

    public function testItSetsWebsite(): void
    {
        $website = new Website('https://example.com');
        $this->juristicPerson->setWebsite($website);
        $this->assertInstanceOf(Website::class, $this->juristicPerson->getWebsite());
        $this->assertSame('https://example.com', (string) $this->juristicPerson->getWebsite());
    }
}
