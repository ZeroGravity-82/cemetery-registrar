<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorFactory;
use Cemetery\Tests\Registrar\Domain\Model\AbstractEntityFactoryTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorFactoryTest extends AbstractEntityFactoryTest
{
    private SoleProprietorFactory $soleProprietorFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->soleProprietorFactory = new SoleProprietorFactory($this->mockIdentityGenerator);
    }

    public function testItCreatesSoleProprietor(): void
    {
        $name                            = 'ИП Иванов Иван Иванович';
        $inn                             = '772208786091';
        $ogrnip                          = '315547600024379';
        $okpo                            = '0137327072';
        $okved                           = '74.82';
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
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $soleProprietor = $this->soleProprietorFactory->create(
            $name,
            $inn,
            $ogrnip,
            $okpo,
            $okved,
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
        $this->assertSame(self::ENTITY_ID, $soleProprietor->id()->value());
        $this->assertSame($name, $soleProprietor->name()->value());
        $this->assertSame($inn, $soleProprietor->inn()->value());
        $this->assertSame($ogrnip, $soleProprietor->ogrnip()->value());
        $this->assertSame($okpo, $soleProprietor->okpo()->value());
        $this->assertSame($okved, $soleProprietor->okved()->value());
        $this->assertSame($registrationAddress, $soleProprietor->registrationAddress()->value());
        $this->assertSame($actualLocationAddress, $soleProprietor->actualLocationAddress()->value());
        $this->assertSame($bankDetailsBankName, $soleProprietor->bankDetails()->bankName()->value());
        $this->assertSame($bankDetailsBik, $soleProprietor->bankDetails()->bik()->value());
        $this->assertSame($bankDetailsCorrespondentAccount, $soleProprietor->bankDetails()->correspondentAccount()->value());
        $this->assertSame($bankDetailsCurrentAccount, $soleProprietor->bankDetails()->currentAccount()->value());
        $this->assertSame($phone, $soleProprietor->phone()->value());
        $this->assertSame($phoneAdditional, $soleProprietor->phoneAdditional()->value());
        $this->assertSame($fax, $soleProprietor->fax()->value());
        $this->assertSame($email, $soleProprietor->email()->value());
        $this->assertSame($website, $soleProprietor->website()->value());
    }

    public function testItCreatesSoleProprietorWithoutOptionalFields(): void
    {
        $name = 'ИП Иванов Иван Иванович';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $soleProprietor = $this->soleProprietorFactory->create(
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
            null,
            null,
        );
        $this->assertInstanceOf(SoleProprietor::class, $soleProprietor);
        $this->assertSame(self::ENTITY_ID, $soleProprietor->id()->value());
        $this->assertSame($name, $soleProprietor->name()->value());
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
}
