<?php

declare(strict_types=1);

namespace DataFixtures\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\Contact\Website;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Model\Organization\Name;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;

class SoleProprietorProvider
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
        $phone           = new PhoneNumber('8(383)133-22-33');
        $phoneAdditional = new PhoneNumber('8(383)133-22-44');
        $fax             = new PhoneNumber('8(383)133-22-55');
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
        $phone                 = new PhoneNumber('8(383)147-22-33');

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
