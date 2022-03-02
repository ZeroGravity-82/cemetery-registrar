<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorBuilder;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorFactoryTest extends TestCase
{
    private MockObject|SoleProprietorBuilder $mockSoleProprietorBuilder;
    private SoleProprietorFactory            $soleProprietorFactory;
    private MockObject|SoleProprietor        $mockSoleProprietor;

    public function setUp(): void
    {
        $this->mockSoleProprietorBuilder = $this->createMock(SoleProprietorBuilder::class);
        $this->soleProprietorFactory     = new SoleProprietorFactory($this->mockSoleProprietorBuilder);
        $this->mockSoleProprietor        = $this->createMock(SoleProprietor::class);
    }

    public function testItCreatesSoleProprietorForCustomer(): void
    {
        $name                            = 'ИП Иванов Иван Иванович';
        $inn                             = '772208786091';
        $ogrnip                          = '315547600024379';
        $registrationAddress             = 'г. Новосибирск, ул. 3 Интернационала, д. 127';
        $actualLocationAddress           = 'г. Москва, ул. Стандартная, д. 21, корп. 1, кв. 5';
        $bankDetailsBankName             = 'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"';
        $bankDetailsBik                  = '045004816';
        $bankDetailsCorrespondentAccount = '30101810500000000816';
        $bankDetailsCurrentAccount       = '40702810904000040651';
        $phone                           = '+7-913-777-88-99';
        $phoneAdditional                 = '8(383)123-45-67';
        $fax                             = '8(383)123-46-89';
        $email                           = 'info@google.com';
        $website                         = 'https://example.com';
        $this->mockSoleProprietorBuilder->expects($this->once())->method('initialize')->with($name);
        $this->mockSoleProprietorBuilder->expects($this->once())->method('addInn')->with($inn);
        $this->mockSoleProprietorBuilder->expects($this->once())->method('addOgrnip')->with($ogrnip);
        $this->mockSoleProprietorBuilder->expects($this->once())->method('addRegistrationAddress')->with($registrationAddress);
        $this->mockSoleProprietorBuilder->expects($this->once())->method('addActualLocationAddress')->with($actualLocationAddress);
        $this->mockSoleProprietorBuilder->expects($this->once())->method('addBankDetails')->with(
            $bankDetailsBankName,
            $bankDetailsBik,
            $bankDetailsCorrespondentAccount,
            $bankDetailsCurrentAccount,
        );
        $this->mockSoleProprietorBuilder->expects($this->once())->method('addPhone')->with($phone);
        $this->mockSoleProprietorBuilder->expects($this->once())->method('addPhoneAdditional')->with($phoneAdditional);
        $this->mockSoleProprietorBuilder->expects($this->once())->method('addFax')->with($fax);
        $this->mockSoleProprietorBuilder->expects($this->once())->method('addEmail')->with($email);
        $this->mockSoleProprietorBuilder->expects($this->once())->method('addWebsite')->with($website);
        $this->mockSoleProprietorBuilder->expects($this->once())->method('build')->willReturn($this->mockSoleProprietor);
        $soleProprietor = $this->soleProprietorFactory->createSoleProprietorForCustomer(
            $name,
            $inn,
            $ogrnip,
            $registrationAddress,
            $actualLocationAddress,
            $bankDetailsBankName,
            $bankDetailsBik,
            $bankDetailsCorrespondentAccount,
            $bankDetailsCurrentAccount,
            $phone,
            $phoneAdditional,
            $fax,
            $email,
            $website,
        );
        $this->assertInstanceOf(SoleProprietor::class, $soleProprietor);
    }

    public function testItCreatesSoleProprietorForCustomerWithoutOptionalFields(): void
    {
        $name = 'ИП Иванов Иван Иванович';
        $this->mockSoleProprietorBuilder->expects($this->once())->method('initialize')->with($name);
        $this->mockSoleProprietorBuilder->expects($this->never())->method('addInn');
        $this->mockSoleProprietorBuilder->expects($this->never())->method('addOgrnip');
        $this->mockSoleProprietorBuilder->expects($this->never())->method('addRegistrationAddress');
        $this->mockSoleProprietorBuilder->expects($this->never())->method('addActualLocationAddress');
        $this->mockSoleProprietorBuilder->expects($this->never())->method('addBankDetails');
        $this->mockSoleProprietorBuilder->expects($this->never())->method('addPhone');
        $this->mockSoleProprietorBuilder->expects($this->never())->method('addPhoneAdditional');
        $this->mockSoleProprietorBuilder->expects($this->never())->method('addFax');
        $this->mockSoleProprietorBuilder->expects($this->never())->method('addEmail');
        $this->mockSoleProprietorBuilder->expects($this->never())->method('addWebsite');
        $this->mockSoleProprietorBuilder->expects($this->once())->method('build')->willReturn($this->mockSoleProprietor);
        $soleProprietor = $this->soleProprietorFactory->createSoleProprietorForCustomer(
            $name,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        );
        $this->assertInstanceOf(SoleProprietor::class, $soleProprietor);
    }

    public function testItFailsToCreateSoleProprietorForCustomerWithoutName(): void
    {
        $this->expectExceptionForNotProvidedName();
        $this->soleProprietorFactory->createSoleProprietorForCustomer(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        );
    }

    private function expectExceptionForNotProvidedName(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Наименование не указано.');
    }
}
