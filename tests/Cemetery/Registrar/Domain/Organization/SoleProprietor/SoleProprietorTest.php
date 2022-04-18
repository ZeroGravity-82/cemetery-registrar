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
        $this->assertInstanceOf(SoleProprietorId::class, $this->soleProprietor->id());
        $this->assertSame('777', (string) $this->soleProprietor->id());
        $this->assertInstanceOf(Name::class, $this->soleProprietor->name());
        $this->assertSame('ИП Иванов Иван Иванович', (string) $this->soleProprietor->name());
        $this->assertNull($this->soleProprietor->inn());
        $this->assertNull($this->soleProprietor->ogrnip());
        $this->assertNull($this->soleProprietor->okpo());
        $this->assertNull($this->soleProprietor->okved());
        $this->assertNull($this->soleProprietor->registrationAddress());
        $this->assertNull($this->soleProprietor->actualLocationAddress());
        $this->assertNull($this->soleProprietor->bankDetails());
        $this->assertNull($this->soleProprietor->phone());
        $this->assertNull($this->soleProprietor->phoneAdditional());
        $this->assertNull($this->soleProprietor->fax());
        $this->assertNull($this->soleProprietor->email());
        $this->assertNull($this->soleProprietor->website());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->soleProprietor->createdAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->soleProprietor->createdAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->soleProprietor->updatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->soleProprietor->updatedAt());
    }

    public function testItSetsInn(): void
    {
        $inn = new Inn('772208786091');
        $this->soleProprietor->setInn($inn);
        $this->assertInstanceOf(Inn::class, $this->soleProprietor->inn());
        $this->assertSame('772208786091', (string) $this->soleProprietor->inn());
    }

    public function testItSetsOgrnip(): void
    {
        $ogrnip = new Ogrnip('315547600024379');
        $this->soleProprietor->setOgrnip($ogrnip);
        $this->assertInstanceOf(Ogrnip::class, $this->soleProprietor->ogrnip());
        $this->assertSame('315547600024379', (string) $this->soleProprietor->ogrnip());
    }

    public function testItSetsOkpo(): void
    {
        $okpo = new Okpo('0137327072');
        $this->soleProprietor->setOkpo($okpo);
        $this->assertInstanceOf(Okpo::class, $this->soleProprietor->okpo());
        $this->assertSame('0137327072', (string) $this->soleProprietor->okpo());
    }

    public function testItSetsOkved(): void
    {
        $okved = new Okved('74.82');
        $this->soleProprietor->setOkved($okved);
        $this->assertInstanceOf(Okved::class, $this->soleProprietor->okved());
        $this->assertSame('74.82', (string) $this->soleProprietor->okved());
    }

    public function testItSetsRegistrationAddress(): void
    {
        $registrationAddress = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $this->soleProprietor->setRegistrationAddress($registrationAddress);
        $this->assertInstanceOf(Address::class, $this->soleProprietor->registrationAddress());
        $this->assertSame(
            'г. Новосибирск, ул. 3 Интернационала, д. 127',
            (string) $this->soleProprietor->registrationAddress()
        );
    }

    public function testItSetsActualLocationAddress(): void
    {
        $actualLocationAddress = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $this->soleProprietor->setActualLocationAddress($actualLocationAddress);
        $this->assertInstanceOf(Address::class, $this->soleProprietor->actualLocationAddress());
        $this->assertSame(
            'г. Новосибирск, ул. 3 Интернационала, д. 127',
            (string) $this->soleProprietor->actualLocationAddress()
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
        $this->assertInstanceOf(BankDetails::class, $this->soleProprietor->bankDetails());
        $this->assertSame(
            'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"',
            (string) $this->soleProprietor->bankDetails()->bankName()
        );
        $this->assertSame('045004816', (string) $this->soleProprietor->bankDetails()->bik());
        $this->assertSame('30101810500000000816', (string) $this->soleProprietor->bankDetails()->correspondentAccount());
        $this->assertSame('40702810904000040651', (string) $this->soleProprietor->bankDetails()->currentAccount());
    }

    public function testItSetsPhone(): void
    {
        $phone = new PhoneNumber('+7-913-777-88-99');
        $this->soleProprietor->setPhone($phone);
        $this->assertInstanceOf(PhoneNumber::class, $this->soleProprietor->phone());
        $this->assertSame('+7-913-777-88-99', (string) $this->soleProprietor->phone());
    }

    public function testItSetsPhoneAdditional(): void
    {
        $phoneAdditional = new PhoneNumber('+7-913-777-88-99');
        $this->soleProprietor->setPhoneAdditional($phoneAdditional);
        $this->assertInstanceOf(PhoneNumber::class, $this->soleProprietor->phoneAdditional());
        $this->assertSame('+7-913-777-88-99', (string) $this->soleProprietor->phoneAdditional());
    }

    public function testItSetsFax(): void
    {
        $fax = new PhoneNumber('+7-913-777-88-99');
        $this->soleProprietor->setFax($fax);
        $this->assertInstanceOf(PhoneNumber::class, $this->soleProprietor->fax());
        $this->assertSame('+7-913-777-88-99', (string) $this->soleProprietor->fax());
    }

    public function testItSetsEmail(): void
    {
        $email = new Email('info@google.com');
        $this->soleProprietor->setEmail($email);
        $this->assertInstanceOf(Email::class, $this->soleProprietor->email());
        $this->assertSame('info@google.com', (string) $this->soleProprietor->email());
    }

    public function testItSetsWebsite(): void
    {
        $website = new Website('https://example.com');
        $this->soleProprietor->setWebsite($website);
        $this->assertInstanceOf(Website::class, $this->soleProprietor->website());
        $this->assertSame('https://example.com', (string) $this->soleProprietor->website());
    }
}
