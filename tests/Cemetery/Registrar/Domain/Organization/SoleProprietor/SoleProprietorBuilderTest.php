<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Domain\IdentityGenerator;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\Okved;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\Ogrnip;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\Okpo;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorBuilder;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorBuilderTest extends TestCase
{
    private MockObject|IdentityGenerator $mockIdentityGenerator;
    private SoleProprietorBuilder        $soleProprietorBuilder;

    public function setUp(): void
    {
        $this->mockIdentityGenerator = $this->createMock(IdentityGenerator::class);
        $this->mockIdentityGenerator->method('getNextIdentity')->willReturn('555');

        $this->soleProprietorBuilder = new SoleProprietorBuilder($this->mockIdentityGenerator);
        $this->soleProprietorBuilder->initialize('ИП Иванов Иван Иванович');
    }

    public function testItInitializesASoleProprietorWithRequiredFields(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->build();

        $this->assertInstanceOf(SoleProprietor::class, $soleProprietor);
        $this->assertInstanceOf(SoleProprietorId::class, $soleProprietor->id());
        $this->assertSame('555', (string) $soleProprietor->id());
        $this->assertInstanceOf(Name::class, $soleProprietor->name());
        $this->assertSame('ИП Иванов Иван Иванович', (string) $soleProprietor->name());
        $this->assertNull($soleProprietor->inn());
        $this->assertNull($soleProprietor->ogrnip());
        $this->assertNull($soleProprietor->okpo());
        $this->assertNull($soleProprietor->okved());
        $this->assertNull($soleProprietor->registrationAddress());
        $this->assertNull($soleProprietor->actualLocationAddress());
        $this->assertNull($soleProprietor->bankDetails());
        $this->assertNull($soleProprietor->phone());
        $this->assertNull($soleProprietor->phoneAdditional());
        $this->assertNull($soleProprietor->fax());
        $this->assertNull($soleProprietor->email());
        $this->assertNull($soleProprietor->website());
    }

    public function testItFailsToBuildASoleProprietorBeforeInitialization(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\sprintf('Строитель для класса %s не инициализирован.', SoleProprietor::class));

        $soleProprietorBuilder = new SoleProprietorBuilder($this->mockIdentityGenerator);
        $soleProprietorBuilder->build();
    }

    public function testItAddsInn(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->addInn('772208786091')->build();
        $this->assertInstanceOf(Inn::class, $soleProprietor->inn());
        $this->assertSame('772208786091', (string) $soleProprietor->inn());
    }

    public function testItAddsOgrnip(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->addOgrnip('315547600024379')->build();
        $this->assertInstanceOf(Ogrnip::class, $soleProprietor->ogrnip());
        $this->assertSame('315547600024379', (string) $soleProprietor->ogrnip());
    }

    public function testItAddsOkpo(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->addOkpo('0137327072')->build();
        $this->assertInstanceOf(Okpo::class, $soleProprietor->okpo());
        $this->assertSame('0137327072', (string) $soleProprietor->okpo());
    }

    public function testItAddsOkved(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->addOkved('74.82')->build();
        $this->assertInstanceOf(Okved::class, $soleProprietor->okved());
        $this->assertSame('74.82', (string) $soleProprietor->okved());
    }

    public function testItAddsRegistrationAddress(): void
    {
        $soleProprietor = $this->soleProprietorBuilder
            ->addRegistrationAddress('г. Новосибирск, ул. Дмитрия Шамшурина, д. 37')
            ->build();
        $this->assertInstanceOf(Address::class, $soleProprietor->registrationAddress());
        $this->assertSame(
            'г. Новосибирск, ул. Дмитрия Шамшурина, д. 37',
            (string) $soleProprietor->registrationAddress()
        );
    }

    public function testItAddsActualLocationAddress(): void
    {
        $soleProprietor = $this->soleProprietorBuilder
            ->addActualLocationAddress('г. Новосибирск, ул. Дмитрия Шамшурина, д. 37')
            ->build();
        $this->assertInstanceOf(Address::class, $soleProprietor->actualLocationAddress());
        $this->assertSame(
            'г. Новосибирск, ул. Дмитрия Шамшурина, д. 37',
            (string) $soleProprietor->actualLocationAddress()
        );
    }

    public function testItAddsBankDetails(): void
    {
        $soleProprietor = $this->soleProprietorBuilder
            ->addBankDetails(
                'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"',
                '045004816',
                '30101810500000000816',
                '40702810904000040651',
            )
            ->build();
        $this->assertInstanceOf(BankDetails::class, $soleProprietor->bankDetails());
        $this->assertSame(
            'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"',
            (string) $soleProprietor->bankDetails()->bankName()
        );
        $this->assertSame('045004816', (string) $soleProprietor->bankDetails()->bik());
        $this->assertSame('30101810500000000816', (string) $soleProprietor->bankDetails()->correspondentAccount());
        $this->assertSame('40702810904000040651', (string) $soleProprietor->bankDetails()->currentAccount());
    }

    public function testItAddsAPhone(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->addPhone('+7-999-555-44-33')->build();
        $this->assertInstanceOf(PhoneNumber::class, $soleProprietor->phone());
        $this->assertSame('+7-999-555-44-33', (string) $soleProprietor->phone());
    }

    public function testItAddsAPhoneAdditional(): void
    {
        $soleProprietor  = $this->soleProprietorBuilder->addPhoneAdditional('+7-999-777-11-22')->build();
        $this->assertInstanceOf(PhoneNumber::class, $soleProprietor->phoneAdditional());
        $this->assertSame('+7-999-777-11-22', (string) $soleProprietor->phoneAdditional());
    }

    public function testItAddsAFax(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->addFax('+7-999-555-44-33')->build();
        $this->assertInstanceOf(PhoneNumber::class, $soleProprietor->fax());
        $this->assertSame('+7-999-555-44-33', (string) $soleProprietor->fax());
    }

    public function testItAddsAnEmail(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->addEmail('info@example.com')->build();
        $this->assertInstanceOf(Email::class, $soleProprietor->email());
        $this->assertSame('info@example.com', (string) $soleProprietor->email());
    }

    public function testItAddsAWebsite(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->addWebsite('https://example.com')->build();
        $this->assertInstanceOf(Website::class, $soleProprietor->website());
        $this->assertSame('https://example.com', (string) $soleProprietor->website());
    }
}
