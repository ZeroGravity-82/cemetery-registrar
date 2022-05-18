<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\NaturalPerson\Passport;

final class NaturalPersonProvider
{
    public static function getNaturalPersonA(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP001');
        $fullName = new FullName('Егоров Абрам Даниилович');

        return new NaturalPerson($id, $fullName);
    }

    public static function getNaturalPersonB(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP002');
        $fullName = new FullName('Устинов Арсений Максович');
        $bornAt   = new \DateTimeImmutable('1918-12-30');

        return (new NaturalPerson($id, $fullName))
            ->setBornAt($bornAt);
    }

    public static function getNaturalPersonC(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP003');
        $fullName = new FullName('Шилов Александр Михаилович');
        $bornAt   = new \DateTimeImmutable('1969-05-20');
        $passport = new Passport(
            '4581',
            '684214',
            new \DateTimeImmutable('2001-03-23'),
            'МВД России по Кемеровской области',
            '681-225',
        );

        return (new NaturalPerson($id, $fullName))
            ->setBornAt($bornAt)
            ->setPassport($passport);
    }

    public static function getNaturalPersonD(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP004');
        $fullName = new FullName('Соколов Герман Маркович');
        $passport = new Passport(
            '1235',
            '567891',
            new \DateTimeImmutable('2011-03-23'),
            'Отделом УФМС России по Новосибирской области в Заельцовском районе',
            '541-001',
        );

        return (new NaturalPerson($id, $fullName))
            ->setPassport($passport);
    }

    public static function getNaturalPersonE(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP005');
        $fullName = new FullName('Жданова Инга Григорьевна');
        $phone    = new PhoneNumber('+7-913-111-22-33');
        $address  = new Address('Новосибирск, ул. Ленина, д. 1');
        $bornAt   = new \DateTimeImmutable('1979-02-12');
        $passport = new Passport(
            '1234',
            '567890',
            new \DateTimeImmutable('2002-10-28'),
            'УВД Кировского района города Новосибирска',
            '540-001',
        );

        return (new NaturalPerson($id, $fullName))
            ->setPhone($phone)
            ->setAddress($address)
            ->setBornAt($bornAt)
            ->setPassport($passport);
    }

    public static function getNaturalPersonF(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP006');
        $fullName = new FullName('Гришина Устинья Ярославовна');

        return new NaturalPerson($id, $fullName);
    }

    public static function getNaturalPersonG(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP007');
        $fullName = new FullName('Громов Никифор Рудольфович');
        $bornAt   = new \DateTimeImmutable('1915-11-24');

        return (new NaturalPerson($id, $fullName))
            ->setBornAt($bornAt);
    }

    public static function getNaturalPersonH(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP008');
        $fullName = new FullName('Беляев Мечеслав Федорович');

        return new NaturalPerson($id, $fullName);
    }
}
