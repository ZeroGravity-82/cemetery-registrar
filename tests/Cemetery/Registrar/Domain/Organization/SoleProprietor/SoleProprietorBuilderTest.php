<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Domain\IdentityGeneratorInterface;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\Ogrnip;
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
    private MockObject|IdentityGeneratorInterface $mockIdentityGenerator;
    private SoleProprietorBuilder                 $soleProprietorBuilder;

    public function setUp(): void
    {
        $this->mockIdentityGenerator = $this->createMock(IdentityGeneratorInterface::class);
        $this->mockIdentityGenerator->method('getNextIdentity')->willReturn('555');

        $this->soleProprietorBuilder = new SoleProprietorBuilder($this->mockIdentityGenerator);
        $this->soleProprietorBuilder->initialize('ИП Иванов Иван Иванович');
    }

    public function testItInitializesASoleProprietorWithRequiredFields(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->build();

        $this->assertInstanceOf(SoleProprietor::class, $soleProprietor);
        $this->assertInstanceOf(SoleProprietorId::class, $soleProprietor->getId());
        $this->assertSame('555', (string) $soleProprietor->getId());
        $this->assertInstanceOf(Name::class, $soleProprietor->getName());
        $this->assertSame('ИП Иванов Иван Иванович', (string) $soleProprietor->getName());
        $this->assertNull($soleProprietor->getInn());
        $this->assertNull($soleProprietor->getOgrnip());
        $this->assertNull($soleProprietor->getRegistrationAddress());
        $this->assertNull($soleProprietor->getActualLocationAddress());
        $this->assertNull($soleProprietor->getBankDetails());
        $this->assertNull($soleProprietor->getPhone());
        $this->assertNull($soleProprietor->getPhoneAdditional());
        $this->assertNull($soleProprietor->getFax());
        $this->assertNull($soleProprietor->getEmail());
        $this->assertNull($soleProprietor->getWebsite());
    }

    public function testItFailsToBuildASoleProprietorBeforeInitialization(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Строитель для класса SoleProprietor не инициализирован.');

        $soleProprietorBuilder = new SoleProprietorBuilder($this->mockIdentityGenerator);
        $soleProprietorBuilder->build();
    }

    public function testItAddsInn(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->addInn('772208786091')->build();
        $this->assertInstanceOf(Inn::class, $soleProprietor->getInn());
        $this->assertSame('772208786091', (string) $soleProprietor->getInn());
    }

    public function testItAddsOgrnip(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->addOgrnip('315547600024379')->build();
        $this->assertInstanceOf(Ogrnip::class, $soleProprietor->getOgrnip());
        $this->assertSame('315547600024379', (string) $soleProprietor->getOgrnip());
    }

    public function testItAddsRegistrationAddress(): void
    {
        $soleProprietor = $this->soleProprietorBuilder
            ->addRegistrationAddress('г. Новосибирск, ул. Дмитрия Шамшурина, д. 37')
            ->build();
        $this->assertInstanceOf(Address::class, $soleProprietor->getRegistrationAddress());
        $this->assertSame(
            'г. Новосибирск, ул. Дмитрия Шамшурина, д. 37',
            (string) $soleProprietor->getRegistrationAddress()
        );
    }

    public function testItAddsActualLocationAddress(): void
    {
        $soleProprietor = $this->soleProprietorBuilder
            ->addActualLocationAddress('г. Новосибирск, ул. Дмитрия Шамшурина, д. 37')
            ->build();
        $this->assertInstanceOf(Address::class, $soleProprietor->getActualLocationAddress());
        $this->assertSame(
            'г. Новосибирск, ул. Дмитрия Шамшурина, д. 37',
            (string) $soleProprietor->getActualLocationAddress()
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
        $this->assertInstanceOf(BankDetails::class, $soleProprietor->getBankDetails());
        $this->assertSame(
            'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"',
            (string) $soleProprietor->getBankDetails()->getBankName()
        );
        $this->assertSame('045004816', (string) $soleProprietor->getBankDetails()->getBik());
        $this->assertSame('30101810500000000816', (string) $soleProprietor->getBankDetails()->getCorrespondentAccount());
        $this->assertSame('40702810904000040651', (string) $soleProprietor->getBankDetails()->getCurrentAccount());
    }

    public function testItAddsAPhone(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->addPhone('+7-999-555-44-33')->build();
        $this->assertInstanceOf(PhoneNumber::class, $soleProprietor->getPhone());
        $this->assertSame('+7-999-555-44-33', (string) $soleProprietor->getPhone());
    }

    public function testItAddsAPhoneAdditional(): void
    {
        $soleProprietor  = $this->soleProprietorBuilder->addPhoneAdditional('+7-999-777-11-22')->build();
        $this->assertInstanceOf(PhoneNumber::class, $soleProprietor->getPhoneAdditional());
        $this->assertSame('+7-999-777-11-22', (string) $soleProprietor->getPhoneAdditional());
    }

    public function testItAddsAFax(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->addFax('+7-999-555-44-33')->build();
        $this->assertInstanceOf(PhoneNumber::class, $soleProprietor->getFax());
        $this->assertSame('+7-999-555-44-33', (string) $soleProprietor->getFax());
    }

    public function testItAddsAnEmail(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->addEmail('info@example.com')->build();
        $this->assertInstanceOf(Email::class, $soleProprietor->getEmail());
        $this->assertSame('info@example.com', (string) $soleProprietor->getEmail());
    }

    public function testItAddsAWebsite(): void
    {
        $soleProprietor = $this->soleProprietorBuilder->addWebsite('https://example.com')->build();
        $this->assertInstanceOf(Website::class, $soleProprietor->getWebsite());
        $this->assertSame('https://example.com', (string) $soleProprietor->getWebsite());
    }
}
