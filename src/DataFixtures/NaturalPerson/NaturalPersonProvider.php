<?php

declare(strict_types=1);

namespace DataFixtures\NaturalPerson;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\Age;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\CremationCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeathCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetails;
use Cemetery\Registrar\Domain\Model\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\Passport;

class NaturalPersonProvider
{
    public static function getNaturalPersonA(): NaturalPerson
    {
        $id                   = new NaturalPersonId('NP001');
        $fullName             = new FullName('Егоров Абрам Даниилович');
        $diedAt               = new \DateTimeImmutable('2021-12-01');
        $age                  = new Age(69);
        $cremationCertificate = new CremationCertificate('12964', new \DateTimeImmutable('2021-12-03'));
        $deceasedDetails      = new DeceasedDetails(
            $diedAt,
            $age,
            null,
            null,
            $cremationCertificate,
        );

        return (new NaturalPerson($id, $fullName))
            ->setDeceasedDetails($deceasedDetails);
    }

    public static function getNaturalPersonB(): NaturalPerson
    {
        $id               = new NaturalPersonId('NP002');
        $fullName         = new FullName('Устинов Иван Максович');
        $bornAt           = new \DateTimeImmutable('1918-12-30');
        $diedAt           = new \DateTimeImmutable('2001-02-12');
        $causeOfDeathId   = new CauseOfDeathId('CD008');
        $deathCertificate = new DeathCertificate('V-МЮ', '532515', new \DateTimeImmutable('2001-02-15'));
        $deceasedDetails  = new DeceasedDetails(
            $diedAt,
            null,
            $causeOfDeathId,
            $deathCertificate,
            null,
        );

        return (new NaturalPerson($id, $fullName))
            ->setBornAt($bornAt)
            ->setDeceasedDetails($deceasedDetails);
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
        $diedAt           = new \DateTimeImmutable('2012-05-13');
        $causeOfDeathId   = new CauseOfDeathId('CD004');
        $deathCertificate = new DeathCertificate('I-BC', '785066', new \DateTimeImmutable('2011-03-23'));
        $deceasedDetails  = new DeceasedDetails(
            $diedAt,
            null,
            $causeOfDeathId,
            $deathCertificate,
            null,
        );

        return (new NaturalPerson($id, $fullName))
            ->setBornAt($bornAt)
            ->setPassport($passport)
            ->setDeceasedDetails($deceasedDetails);
    }

    public static function getNaturalPersonD(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP004');
        $fullName = new FullName('Соколов Герман Маркович');
        $passport = new Passport(
            '1235',
            '567891',
            new \DateTimeImmutable('2001-02-23'),
            'Отделом УФМС России по Новосибирской области в Заельцовском районе',
            '541-001',
        );
        $diedAt          = new \DateTimeImmutable('2010-01-26');
        $causeOfDeathId  = new CauseOfDeathId('CD004');
        $deceasedDetails = new DeceasedDetails(
            $diedAt,
            null,
            $causeOfDeathId,
            null,
            null,
        );

        return (new NaturalPerson($id, $fullName))
            ->setPassport($passport)
            ->setDeceasedDetails($deceasedDetails);
    }

    public static function getNaturalPersonE(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP005');
        $fullName = new FullName('Жданова Инга Григорьевна');
        $phone    = new PhoneNumber('8-913-771-22-33');
        $address  = new Address('Новосибирск, Ленина 1');
        $bornAt   = new \DateTimeImmutable('1980-02-12');
        $passport = new Passport(
            '1234',
            '567890',
            new \DateTimeImmutable('2002-10-28'),
            'УВД Кировского района города Новосибирска',
            '540-001',
        );
        $diedAt          = new \DateTimeImmutable('2022-03-10');
        $deceasedDetails = new DeceasedDetails(
            $diedAt,
            null,
            null,
            null,
            null,
        );

        return (new NaturalPerson($id, $fullName))
            ->setPhone($phone)
            ->setAddress($address)
            ->setBornAt($bornAt)
            ->setPassport($passport)
            ->setDeceasedDetails($deceasedDetails);
    }

    public static function getNaturalPersonF(): NaturalPerson
    {
        $id              = new NaturalPersonId('NP006');
        $fullName        = new FullName('Гришина Устинья Ярославовна');
        $diedAt          = new \DateTimeImmutable('2021-12-03');
        $deceasedDetails = new DeceasedDetails(
            $diedAt,
            null,
            null,
            null,
            null,
        );

        return (new NaturalPerson($id, $fullName))
            ->setDeceasedDetails($deceasedDetails);
    }

    public static function getNaturalPersonG(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP007');
        $fullName = new FullName('Громов Никифор Рудольфович');
        $address  = new Address('Новосибирск, Н.-Данченко 18 - 17');
        $bornAt   = new \DateTimeImmutable('1915-09-24');

        return (new NaturalPerson($id, $fullName))
            ->setAddress($address)
            ->setBornAt($bornAt);
    }

    public static function getNaturalPersonH(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP008');
        $fullName = new FullName('Беляев Мечеслав Федорович');
        $email    = new Email('mecheslav.belyaev@gmail.com');
        $passport = new Passport(
            '2345',
            '162354',
            new \DateTimeImmutable('1981-10-20'),
            'Отделом МВД Ленинского района г. Пензы',
            null,
        );

        return (new NaturalPerson($id, $fullName))
            ->setEmail($email)
            ->setPassport($passport);
    }

    public static function getNaturalPersonI(): NaturalPerson
    {
        $id              = new NaturalPersonId('NP009');
        $fullName        = new FullName('Никонов Родион Митрофанович');
        $diedAt          = new \DateTimeImmutable('1980-05-26');
        $deceasedDetails = new DeceasedDetails(
            $diedAt,
            null,
            null,
            null,
            null,
        );

        return (new NaturalPerson($id, $fullName))
            ->setDeceasedDetails($deceasedDetails);
    }

    public static function getNaturalPersonJ(): NaturalPerson
    {
        $id              = new NaturalPersonId('NP010');
        $fullName        = new FullName('Иванов Иван Иванович');
        $bornAt          = new \DateTimeImmutable('1930-11-04');
        $diedAt          = new \DateTimeImmutable('2002-11-22');
        $deceasedDetails = new DeceasedDetails(
            $diedAt,
            null,
            null,
            null,
            null,
        );

        return (new NaturalPerson($id, $fullName))
            ->setBornAt($bornAt)
            ->setDeceasedDetails($deceasedDetails);
    }

    public static function getNaturalPersonK(): NaturalPerson
    {
        $id              = new NaturalPersonId('NP011');
        $fullName        = new FullName('Иванов Иван Иванович');
        $bornAt          = new \DateTimeImmutable('1925-04-12');
        $diedAt          = new \DateTimeImmutable('2004-05-11');
        $deceasedDetails = new DeceasedDetails(
            $diedAt,
            null,
            null,
            null,
            null,
        );

        return (new NaturalPerson($id, $fullName))
            ->setBornAt($bornAt)
            ->setDeceasedDetails($deceasedDetails);
    }

    public static function getNaturalPersonL(): NaturalPerson
    {
        $id              = new NaturalPersonId('NP012');
        $fullName        = new FullName('Иванов Иван Иванович');
        $bornAt          = new \DateTimeImmutable('1925-04-12');
        $diedAt          = new \DateTimeImmutable('2005-10-29');
        $deceasedDetails = new DeceasedDetails(
            $diedAt,
            null,
            null,
            null,
            null,
        );

        return (new NaturalPerson($id, $fullName))
            ->setBornAt($bornAt)
            ->setDeceasedDetails($deceasedDetails);
    }

    public static function getNaturalPersonM(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP013');
        $fullName = new FullName('Петров Пётр Петрович');

        return new NaturalPerson($id, $fullName);
    }
}
