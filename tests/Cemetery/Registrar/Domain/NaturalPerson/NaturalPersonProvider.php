<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;

final class NaturalPersonProvider
{
    public static function getNaturalPersonA(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP001');
        $fullName = new FullName('Ivanov Ivan Ivanovich');

        return new NaturalPerson($id, $fullName, null);
    }

    public static function getNaturalPersonB(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP002');
        $fullName = new FullName('Petrov Petr Petrovich');
        $bornAt   = new \DateTimeImmutable('1998-12-30');

        return new NaturalPerson($id, $fullName, $bornAt);
    }

    public static function getNaturalPersonC(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP003');
        $fullName = new FullName('Sidorov Sidr Sidorovich');
        $bornAt   = new \DateTimeImmutable('2005-05-20');

        return new NaturalPerson($id, $fullName, $bornAt);
    }

    public static function getNaturalPersonD(): NaturalPerson
    {
        $id       = new NaturalPersonId('NP004');
        $fullName = new FullName('Sokolov German Markovich');

        return new NaturalPerson($id, $fullName, null);
    }
}
