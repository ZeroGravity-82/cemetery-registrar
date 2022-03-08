<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\Inn;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;

final class JuristicPersonProvider
{
    public static function getJuristicPersonA(): JuristicPerson
    {
        $id   = new JuristicPersonId('JP001');
        $name = new Name('ООО "Рога и копыта"');

        return new JuristicPerson($id, $name);
    }

    public static function getJuristicPersonB(): JuristicPerson
    {
        $id          = new JuristicPersonId('JP002');
        $name        = new Name('ООО Ромашка');
        $inn         = new Inn('5404447629');
        $bankDetails = new BankDetails('АО "АЛЬФА-БАНК"', '044525593', '30101810200000000593', '40701810401400000014');

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
}
