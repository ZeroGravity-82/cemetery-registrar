<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;

final class SoleProprietorProvider
{
    public static function getSoleProprietorA(): SoleProprietor
    {
        $id   = new SoleProprietorId('SP001');
        $name = new Name('ИП Иванов Иван Иванович');

        return new SoleProprietor($id, $name);
    }

    public static function getSoleProprietorB(): SoleProprietor
    {
        $id              = new SoleProprietorId('SP002');
        $name            = new Name('ИП Петров Пётр Петрович');
        $inn             = new Inn('772208786091');
        $bankDetails     = new BankDetails('АО "АЛЬФА-БАНК"', '044525593', '30101810200000000593', '40701810401400000014');
        $phone           = new PhoneNumber('8(383)111-22-33');
        $phoneAdditional = new PhoneNumber('8(383)111-22-44');
        $fax             = new PhoneNumber('8(383)111-22-55');
        $email           = new Email('info@funeral54.ru');
        $website         = new Website('funeral54.ru');

        return (new SoleProprietor($id, $name))
            ->setInn($inn)
            ->setBankDetails($bankDetails)
            ->setPhone($phone)
            ->setPhoneAdditional($phoneAdditional)
            ->setFax($fax)
            ->setEmail($email)
            ->setWebsite($website);
    }

    public static function getSoleProprietorC(): SoleProprietor
    {
        $id                    = new SoleProprietorId('SP003');
        $name                  = new Name('ИП Сидоров Сидр Сидорович');
        $inn                   = new Inn('391600743661');
        $actualLocationAddress = new Address('с. Каменка, д. 14');
        $phone                 = new PhoneNumber('8(383)111-22-33');

        return (new SoleProprietor($id, $name))
            ->setInn($inn)
            ->setActualLocationAddress($actualLocationAddress)
            ->setPhone($phone);
    }

    public static function getSoleProprietorD(): SoleProprietor
    {
        $id   = new SoleProprietorId('SP004');
        $name = new Name('ИП Соколов Герман Маркович');

        return new SoleProprietor($id, $name);
    }
}
