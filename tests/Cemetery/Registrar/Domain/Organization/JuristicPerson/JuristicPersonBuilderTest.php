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
        $this->assertInstanceOf(JuristicPersonId::class, $juristicPerson->getId());
        $this->assertSame('555', (string) $juristicPerson->getId());
        $this->assertInstanceOf(Name::class, $juristicPerson->getName());
        $this->assertSame('ООО "Рога и копыта"', (string) $juristicPerson->getName());
        $this->assertNull($juristicPerson->getInn());
        $this->assertNull($juristicPerson->getKpp());
        $this->assertNull($juristicPerson->getOgrn());
        $this->assertNull($juristicPerson->getOkpo());
        $this->assertNull($juristicPerson->getOkved());
        $this->assertNull($juristicPerson->getLegalAddress());
        $this->assertNull($juristicPerson->getPostalAddress());
        $this->assertNull($juristicPerson->getBankDetails());
        $this->assertNull($juristicPerson->getPhone());
        $this->assertNull($juristicPerson->getPhoneAdditional());
        $this->assertNull($juristicPerson->getFax());
        $this->assertNull($juristicPerson->getGeneralDirector());
        $this->assertNull($juristicPerson->getEmail());
        $this->assertNull($juristicPerson->getWebsite());
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
        $this->assertInstanceOf(Inn::class, $juristicPerson->getInn());
        $this->assertSame('5409241558', (string) $juristicPerson->getInn());
    }

    public function testItAddsKpp(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addKpp('540901001')->build();
        $this->assertInstanceOf(Kpp::class, $juristicPerson->getKpp());
        $this->assertSame('540901001', (string) $juristicPerson->getKpp());
    }

    public function testItAddsOgrn(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addOgrn('1145476118260')->build();
        $this->assertInstanceOf(Ogrn::class, $juristicPerson->getOgrn());
        $this->assertSame('1145476118260', (string) $juristicPerson->getOgrn());
    }

    public function testItAddsOkpo(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addOkpo('39134998')->build();
        $this->assertInstanceOf(Okpo::class, $juristicPerson->getOkpo());
        $this->assertSame('39134998', (string) $juristicPerson->getOkpo());
    }

    public function testItAddsOkved(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addOkved('74.82')->build();
        $this->assertInstanceOf(Okved::class, $juristicPerson->getOkved());
        $this->assertSame('74.82', (string) $juristicPerson->getOkved());
    }

    public function testItAddsLegalAddress(): void
    {
        $juristicPerson = $this->juristicPersonBuilder
            ->addLegalAddress('г. Новосибирск, ул. Дмитрия Шамшурина, д. 37')
            ->build();
        $this->assertInstanceOf(Address::class, $juristicPerson->getLegalAddress());
        $this->assertSame(
            'г. Новосибирск, ул. Дмитрия Шамшурина, д. 37',
            (string) $juristicPerson->getLegalAddress()
        );
    }

    public function testItAddsPostalAddress(): void
    {
        $juristicPerson = $this->juristicPersonBuilder
            ->addPostalAddress('г. Новосибирск, ул. Дмитрия Шамшурина, д. 37')
            ->build();
        $this->assertInstanceOf(Address::class, $juristicPerson->getPostalAddress());
        $this->assertSame(
            'г. Новосибирск, ул. Дмитрия Шамшурина, д. 37',
            (string) $juristicPerson->getPostalAddress()
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
        $this->assertInstanceOf(BankDetails::class, $juristicPerson->getBankDetails());
        $this->assertSame(
            'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"',
            (string) $juristicPerson->getBankDetails()->getBankName()
        );
        $this->assertSame('045004816', (string) $juristicPerson->getBankDetails()->getBik());
        $this->assertSame('30101810500000000816', (string) $juristicPerson->getBankDetails()->getCorrespondentAccount());
        $this->assertSame('40702810904000040651', (string) $juristicPerson->getBankDetails()->getCurrentAccount());
    }

    public function testItAddsAPhone(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addPhone('+7-999-555-44-33')->build();
        $this->assertInstanceOf(PhoneNumber::class, $juristicPerson->getPhone());
        $this->assertSame('+7-999-555-44-33', (string) $juristicPerson->getPhone());
    }

    public function testItAddsAPhoneAdditional(): void
    {
        $juristicPerson  = $this->juristicPersonBuilder->addPhoneAdditional('+7-999-777-11-22')->build();
        $this->assertInstanceOf(PhoneNumber::class, $juristicPerson->getPhoneAdditional());
        $this->assertSame('+7-999-777-11-22', (string) $juristicPerson->getPhoneAdditional());
    }

    public function testItAddsAFax(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addFax('+7-999-555-44-33')->build();
        $this->assertInstanceOf(PhoneNumber::class, $juristicPerson->getFax());
        $this->assertSame('+7-999-555-44-33', (string) $juristicPerson->getFax());
    }

    public function testItAddsAGeneralDirector(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addGeneralDirector('Иванов Иван Иванович')->build();
        $this->assertInstanceOf(FullName::class, $juristicPerson->getGeneralDirector());
        $this->assertSame('Иванов Иван Иванович', (string) $juristicPerson->getGeneralDirector());
    }

    public function testItAddsAnEmail(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addEmail('info@example.com')->build();
        $this->assertInstanceOf(Email::class, $juristicPerson->getEmail());
        $this->assertSame('info@example.com', (string) $juristicPerson->getEmail());
    }

    public function testItAddsAWebsite(): void
    {
        $juristicPerson = $this->juristicPersonBuilder->addWebsite('https://example.com')->build();
        $this->assertInstanceOf(Website::class, $juristicPerson->getWebsite());
        $this->assertSame('https://example.com', (string) $juristicPerson->getWebsite());
    }
}
