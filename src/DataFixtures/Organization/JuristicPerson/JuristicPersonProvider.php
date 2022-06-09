<?php

declare(strict_types=1);

namespace DataFixtures\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\Inn;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\Kpp;
use Cemetery\Registrar\Domain\Organization\Name;

class JuristicPersonProvider
{
    public static function getJuristicPersonA(): JuristicPerson
    {
        $id            = new JuristicPersonId('JP001');
        $name          = new Name('ООО "Рога и копыта"');
        $postalAddress = new Address('г. Кемерово, пр. Строителей, д. 5, офис 102');

        return (new JuristicPerson($id, $name))
            ->setPostalAddress($postalAddress);
    }

    public static function getJuristicPersonB(): JuristicPerson
    {
        $id          = new JuristicPersonId('JP002');
        $name        = new Name('ООО Ромашка');
        $inn         = new Inn('5404447629');
        $bankDetails = new BankDetails('ОТДЕЛЕНИЕ ЛЕНИНГРАДСКОЕ БАНКА РОССИИ', '044106001', null, '40601810900001000022');

        return (new JuristicPerson($id, $name))
            ->setInn($inn)
            ->setBankDetails($bankDetails);
    }

    public static function getJuristicPersonC(): JuristicPerson
    {
        $id   = new JuristicPersonId('JP003');
        $name = new Name('ПАО "ГАЗПРОМ"');
        $inn  = new Inn('7736050003');

        return (new JuristicPerson($id, $name))
            ->setInn($inn);
    }

    public static function getJuristicPersonD(): JuristicPerson
    {
        $id   = new JuristicPersonId('JP004');
        $name = new Name('МУП "Новосибирский метрополитен"');

        return new JuristicPerson($id, $name);
    }

    public static function getJuristicPersonE(): JuristicPerson
    {
        $id              = new JuristicPersonId('JP005');
        $name            = new Name('МУП Похоронный Дом "ИМИ"');
        $inn             = new Inn('5402103598');
        $kpp             = new Kpp('540201001');
        $generalDirector = new FullName('Бондаренко Сергей Валентинович');

        return (new JuristicPerson($id, $name))
            ->setInn($inn)
            ->setKpp($kpp)
            ->setGeneralDirector($generalDirector);
    }
}
