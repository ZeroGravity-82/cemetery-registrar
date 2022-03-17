<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\Okved;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\Ogrnip;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\Okpo;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Tests\Registrar\Domain\AbstractAggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorTest extends AbstractAggregateRootTest
{
    private SoleProprietor $soleProprietor;

    public function setUp(): void
    {
        $soleProprietorId       = new SoleProprietorId('777');
        $soleProprietorFullName = new Name('ИП Иванов Иван Иванович');
        $this->soleProprietor   = new SoleProprietor($soleProprietorId, $soleProprietorFullName);
        $this->entity           = $this->soleProprietor;
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(SoleProprietorId::class, $this->soleProprietor->getId());
        $this->assertSame('777', (string) $this->soleProprietor->getId());
        $this->assertInstanceOf(Name::class, $this->soleProprietor->getName());
        $this->assertSame('ИП Иванов Иван Иванович', (string) $this->soleProprietor->getName());
        $this->assertNull($this->soleProprietor->getInn());
        $this->assertNull($this->soleProprietor->getOgrnip());
        $this->assertNull($this->soleProprietor->getOkpo());
        $this->assertNull($this->soleProprietor->getOkved());
        $this->assertNull($this->soleProprietor->getRegistrationAddress());
        $this->assertNull($this->soleProprietor->getActualLocationAddress());
        $this->assertNull($this->soleProprietor->getBankDetails());
        $this->assertNull($this->soleProprietor->getPhone());
        $this->assertNull($this->soleProprietor->getPhoneAdditional());
        $this->assertNull($this->soleProprietor->getFax());
        $this->assertNull($this->soleProprietor->getEmail());
        $this->assertNull($this->soleProprietor->getWebsite());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->soleProprietor->getCreatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->soleProprietor->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->soleProprietor->getUpdatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->soleProprietor->getUpdatedAt());
    }

    public function testItSetsInn(): void
    {
        $inn = new Inn('772208786091');
        $this->soleProprietor->setInn($inn);
        $this->assertInstanceOf(Inn::class, $this->soleProprietor->getInn());
        $this->assertSame('772208786091', (string) $this->soleProprietor->getInn());
    }

    public function testItSetsOgrnip(): void
    {
        $ogrnip = new Ogrnip('315547600024379');
        $this->soleProprietor->setOgrnip($ogrnip);
        $this->assertInstanceOf(Ogrnip::class, $this->soleProprietor->getOgrnip());
        $this->assertSame('315547600024379', (string) $this->soleProprietor->getOgrnip());
    }

    public function testItSetsOkpo(): void
    {
        $okpo = new Okpo('0137327072');
        $this->soleProprietor->setOkpo($okpo);
        $this->assertInstanceOf(Okpo::class, $this->soleProprietor->getOkpo());
        $this->assertSame('0137327072', (string) $this->soleProprietor->getOkpo());
    }

    public function testItSetsOkved(): void
    {
        $okved = new Okved('74.82');
        $this->soleProprietor->setOkved($okved);
        $this->assertInstanceOf(Okved::class, $this->soleProprietor->getOkved());
        $this->assertSame('74.82', (string) $this->soleProprietor->getOkved());
    }

    public function testItSetsRegistrationAddress(): void
    {
        $registrationAddress = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $this->soleProprietor->setRegistrationAddress($registrationAddress);
        $this->assertInstanceOf(Address::class, $this->soleProprietor->getRegistrationAddress());
        $this->assertSame(
            'г. Новосибирск, ул. 3 Интернационала, д. 127',
            (string) $this->soleProprietor->getRegistrationAddress()
        );
    }

    public function testItSetsActualLocationAddress(): void
    {
        $actualLocationAddress = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $this->soleProprietor->setActualLocationAddress($actualLocationAddress);
        $this->assertInstanceOf(Address::class, $this->soleProprietor->getActualLocationAddress());
        $this->assertSame(
            'г. Новосибирск, ул. 3 Интернационала, д. 127',
            (string) $this->soleProprietor->getActualLocationAddress()
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
        $this->soleProprietor->setBankDetails($bankDetails);
        $this->assertInstanceOf(BankDetails::class, $this->soleProprietor->getBankDetails());
        $this->assertSame(
            'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"',
            (string) $this->soleProprietor->getBankDetails()->getBankName()
        );
        $this->assertSame('045004816', (string) $this->soleProprietor->getBankDetails()->getBik());
        $this->assertSame('30101810500000000816', (string) $this->soleProprietor->getBankDetails()->getCorrespondentAccount());
        $this->assertSame('40702810904000040651', (string) $this->soleProprietor->getBankDetails()->getCurrentAccount());
    }

    public function testItSetsPhone(): void
    {
        $phone = new PhoneNumber('+7-913-777-88-99');
        $this->soleProprietor->setPhone($phone);
        $this->assertInstanceOf(PhoneNumber::class, $this->soleProprietor->getPhone());
        $this->assertSame('+7-913-777-88-99', (string) $this->soleProprietor->getPhone());
    }

    public function testItSetsPhoneAdditional(): void
    {
        $phoneAdditional = new PhoneNumber('+7-913-777-88-99');
        $this->soleProprietor->setPhoneAdditional($phoneAdditional);
        $this->assertInstanceOf(PhoneNumber::class, $this->soleProprietor->getPhoneAdditional());
        $this->assertSame('+7-913-777-88-99', (string) $this->soleProprietor->getPhoneAdditional());
    }

    public function testItSetsFax(): void
    {
        $fax = new PhoneNumber('+7-913-777-88-99');
        $this->soleProprietor->setFax($fax);
        $this->assertInstanceOf(PhoneNumber::class, $this->soleProprietor->getFax());
        $this->assertSame('+7-913-777-88-99', (string) $this->soleProprietor->getFax());
    }

    public function testItSetsEmail(): void
    {
        $email = new Email('info@google.com');
        $this->soleProprietor->setEmail($email);
        $this->assertInstanceOf(Email::class, $this->soleProprietor->getEmail());
        $this->assertSame('info@google.com', (string) $this->soleProprietor->getEmail());
    }

    public function testItSetsWebsite(): void
    {
        $website = new Website('https://example.com');
        $this->soleProprietor->setWebsite($website);
        $this->assertInstanceOf(Website::class, $this->soleProprietor->getWebsite());
        $this->assertSame('https://example.com', (string) $this->soleProprietor->getWebsite());
    }
}
