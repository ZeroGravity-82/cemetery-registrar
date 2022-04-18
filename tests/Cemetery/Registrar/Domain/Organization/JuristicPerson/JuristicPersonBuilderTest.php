<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Domain\IdentityGeneratorInterface;
use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\Inn;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\Kpp;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\Ogrn;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\Okpo;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonBuilder;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\Okved;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonBuilderTest extends TestCase
{
    private MockObject|IdentityGeneratorInterface $mockIdentityGenerator;
    private JuristicPersonBuilder                 $juristicPersonBuilder;

    public function setUp(): void
    {
        $this->mockIdentityGenerator = $this->createMock(IdentityGeneratorInterface::class);
        $this->mockIdentityGenerator->method('getNextIdentity')->willReturn('555');

        $this->juristicPersonBuilder = new JuristicPersonBuilder($this->mockIdentityGenerator);
        $this->juristicPersonBuilder->initialize('ООО "Рога и копыта"');
    }

    public function testItInitializesASoleProprietorWithRequiredFields(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->build();

        $this->assertInstanceOf(JuristicPerson::class, $juristicPerson);
        $this->assertInstanceOf(JuristicPersonId::class, $juristicPerson->id());
        $this->assertSame('555', (string) $juristicPerson->id());
        $this->assertInstanceOf(Name::class, $juristicPerson->name());
        $this->assertSame('ООО "Рога и копыта"', (string) $juristicPerson->name());
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

    public function testItFailsToBuildASoleProprietorBeforeInitialization(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\sprintf('Строитель для класса %s не инициализирован.', JuristicPerson::class));

        $juristicPersonBuilder = new JuristicPersonBuilder($this->mockIdentityGenerator);
        $juristicPersonBuilder->build();
    }

    public function testItAddsInn(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addInn('5409241558')->build();
        $this->assertInstanceOf(Inn::class, $juristicPerson->inn());
        $this->assertSame('5409241558', (string) $juristicPerson->inn());
    }

    public function testItAddsKpp(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addKpp('540901001')->build();
        $this->assertInstanceOf(Kpp::class, $juristicPerson->kpp());
        $this->assertSame('540901001', (string) $juristicPerson->kpp());
    }

    public function testItAddsOgrn(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addOgrn('1145476118260')->build();
        $this->assertInstanceOf(Ogrn::class, $juristicPerson->ogrn());
        $this->assertSame('1145476118260', (string) $juristicPerson->ogrn());
    }

    public function testItAddsOkpo(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addOkpo('39134998')->build();
        $this->assertInstanceOf(Okpo::class, $juristicPerson->okpo());
        $this->assertSame('39134998', (string) $juristicPerson->okpo());
    }

    public function testItAddsOkved(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addOkved('74.82')->build();
        $this->assertInstanceOf(Okved::class, $juristicPerson->okved());
        $this->assertSame('74.82', (string) $juristicPerson->okved());
    }

    public function testItAddsLegalAddress(): void
    {
        $juristicPerson = $this->juristicPersonBuilder
            ->addLegalAddress('г. Новосибирск, ул. Дмитрия Шамшурина, д. 37')
            ->build();
        $this->assertInstanceOf(Address::class, $juristicPerson->legalAddress());
        $this->assertSame(
            'г. Новосибирск, ул. Дмитрия Шамшурина, д. 37',
            (string) $juristicPerson->legalAddress()
        );
    }

    public function testItAddsPostalAddress(): void
    {
        $juristicPerson = $this->juristicPersonBuilder
            ->addPostalAddress('г. Новосибирск, ул. Дмитрия Шамшурина, д. 37')
            ->build();
        $this->assertInstanceOf(Address::class, $juristicPerson->postalAddress());
        $this->assertSame(
            'г. Новосибирск, ул. Дмитрия Шамшурина, д. 37',
            (string) $juristicPerson->postalAddress()
        );
    }

    public function testItAddsBankDetails(): void
    {
        $juristicPerson = $this->juristicPersonBuilder
            ->addBankDetails(
                'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"',
                '045004816',
                '30101810500000000816',
                '40702810904000040651',
            )
            ->build();
        $this->assertInstanceOf(BankDetails::class, $juristicPerson->bankDetails());
        $this->assertSame(
            'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"',
            (string) $juristicPerson->bankDetails()->bankName()
        );
        $this->assertSame('045004816', (string) $juristicPerson->bankDetails()->bik());
        $this->assertSame('30101810500000000816', (string) $juristicPerson->bankDetails()->correspondentAccount());
        $this->assertSame('40702810904000040651', (string) $juristicPerson->bankDetails()->currentAccount());
    }

    public function testItAddsAPhone(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addPhone('+7-999-555-44-33')->build();
        $this->assertInstanceOf(PhoneNumber::class, $juristicPerson->phone());
        $this->assertSame('+7-999-555-44-33', (string) $juristicPerson->phone());
    }

    public function testItAddsAPhoneAdditional(): void
    {
        $juristicPerson  = $this->juristicPersonBuilder->addPhoneAdditional('+7-999-777-11-22')->build();
        $this->assertInstanceOf(PhoneNumber::class, $juristicPerson->phoneAdditional());
        $this->assertSame('+7-999-777-11-22', (string) $juristicPerson->phoneAdditional());
    }

    public function testItAddsAFax(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addFax('+7-999-555-44-33')->build();
        $this->assertInstanceOf(PhoneNumber::class, $juristicPerson->fax());
        $this->assertSame('+7-999-555-44-33', (string) $juristicPerson->fax());
    }

    public function testItAddsAGeneralDirector(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addGeneralDirector('Иванов Иван Иванович')->build();
        $this->assertInstanceOf(FullName::class, $juristicPerson->generalDirector());
        $this->assertSame('Иванов Иван Иванович', (string) $juristicPerson->generalDirector());
    }

    public function testItAddsAnEmail(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addEmail('info@example.com')->build();
        $this->assertInstanceOf(Email::class, $juristicPerson->email());
        $this->assertSame('info@example.com', (string) $juristicPerson->email());
    }

    public function testItAddsAWebsite(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addWebsite('https://example.com')->build();
        $this->assertInstanceOf(Website::class, $juristicPerson->website());
        $this->assertSame('https://example.com', (string) $juristicPerson->website());
    }
}
