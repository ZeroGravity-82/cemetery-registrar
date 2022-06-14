<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\Contact\Website;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Model\Organization\Name;
use Cemetery\Registrar\Domain\Model\Organization\Okved;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\Ogrnip;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\Okpo;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorTest extends AggregateRootTest
{
    private SoleProprietor $soleProprietor;

    public function setUp(): void
    {
        $soleProprietorId       = new SoleProprietorId('777');
        $soleProprietorFullName = new Name('ИП Иванов Иван Иванович');
        $this->soleProprietor   = new SoleProprietor($soleProprietorId, $soleProprietorFullName);
        $this->entity           = $this->soleProprietor;
    }

    public function testItHasValidClassShortcutConstant(): void
    {
        $this->assertSame('SOLE_PROPRIETOR', SoleProprietor::CLASS_SHORTCUT);
    }

    public function testItHasValidClassLabelConstant(): void
    {
        $this->assertSame('ИП', SoleProprietor::CLASS_LABEL);
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
    }

    public function testItSetsInn(): void
    {
        $inn = new Inn('772208786091');
        $this->soleProprietor->setInn($inn);
        $this->assertInstanceOf(Inn::class, $this->soleProprietor->inn());
        $this->assertTrue($this->soleProprietor->inn()->isEqual($inn));
    }

    public function testItSetsOgrnip(): void
    {
        $ogrnip = new Ogrnip('315547600024379');
        $this->soleProprietor->setOgrnip($ogrnip);
        $this->assertInstanceOf(Ogrnip::class, $this->soleProprietor->ogrnip());
        $this->assertTrue($this->soleProprietor->ogrnip()->isEqual($ogrnip));
    }

    public function testItSetsOkpo(): void
    {
        $okpo = new Okpo('0137327072');
        $this->soleProprietor->setOkpo($okpo);
        $this->assertInstanceOf(Okpo::class, $this->soleProprietor->okpo());
        $this->assertTrue($this->soleProprietor->okpo()->isEqual($okpo));
    }

    public function testItSetsOkved(): void
    {
        $okved = new Okved('74.82');
        $this->soleProprietor->setOkved($okved);
        $this->assertInstanceOf(Okved::class, $this->soleProprietor->okved());
        $this->assertTrue($this->soleProprietor->okved()->isEqual($okved));
    }

    public function testItSetsRegistrationAddress(): void
    {
        $registrationAddress = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $this->soleProprietor->setRegistrationAddress($registrationAddress);
        $this->assertInstanceOf(Address::class, $this->soleProprietor->registrationAddress());
        $this->assertTrue($this->soleProprietor->registrationAddress()->isEqual($registrationAddress));
    }

    public function testItSetsActualLocationAddress(): void
    {
        $actualLocationAddress = new Address('г. Новосибирск, ул. 3 Интернационала, д. 127');
        $this->soleProprietor->setActualLocationAddress($actualLocationAddress);
        $this->assertInstanceOf(Address::class, $this->soleProprietor->actualLocationAddress());
        $this->assertTrue($this->soleProprietor->actualLocationAddress()->isEqual($actualLocationAddress));
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
        $this->assertTrue($this->soleProprietor->bankDetails()->isEqual($bankDetails));
    }

    public function testItSetsPhone(): void
    {
        $phone = new PhoneNumber('+7-913-777-88-99');
        $this->soleProprietor->setPhone($phone);
        $this->assertInstanceOf(PhoneNumber::class, $this->soleProprietor->phone());
        $this->assertTrue($this->soleProprietor->phone()->isEqual($phone));
    }

    public function testItSetsPhoneAdditional(): void
    {
        $phoneAdditional = new PhoneNumber('+7-913-777-88-99');
        $this->soleProprietor->setPhoneAdditional($phoneAdditional);
        $this->assertInstanceOf(PhoneNumber::class, $this->soleProprietor->phoneAdditional());
        $this->assertTrue($this->soleProprietor->phoneAdditional()->isEqual($phoneAdditional));
    }

    public function testItSetsFax(): void
    {
        $fax = new PhoneNumber('+7-913-777-88-99');
        $this->soleProprietor->setFax($fax);
        $this->assertInstanceOf(PhoneNumber::class, $this->soleProprietor->fax());
        $this->assertTrue($this->soleProprietor->fax()->isEqual($fax));
    }

    public function testItSetsEmail(): void
    {
        $email = new Email('info@google.com');
        $this->soleProprietor->setEmail($email);
        $this->assertInstanceOf(Email::class, $this->soleProprietor->email());
        $this->assertTrue($this->soleProprietor->email()->isEqual($email));
    }

    public function testItSetsWebsite(): void
    {
        $website = new Website('https://example.com');
        $this->soleProprietor->setWebsite($website);
        $this->assertInstanceOf(Website::class, $this->soleProprietor->website());
        $this->assertTrue($this->soleProprietor->website()->isEqual($website));
    }
}
