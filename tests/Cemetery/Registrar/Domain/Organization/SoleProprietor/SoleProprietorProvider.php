<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor;

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
        $id   = new SoleProprietorId('SP002');
        $name = new Name('ИП Петров Пётр Петрович');
        $inn  = new Inn('772208786091');

        return (new SoleProprietor($id, $name))
            ->setInn($inn);
    }

    public static function getSoleProprietorC(): SoleProprietor
    {
        $id   = new SoleProprietorId('SP003');
        $name = new Name('ИП Сидоров Сидр Сидорович');
        $inn  = new Inn('391600743661');

        return (new SoleProprietor($id, $name))
            ->setInn($inn);
    }

    public static function getSoleProprietorD(): SoleProprietor
    {
        $id   = new SoleProprietorId('SP004');
        $name = new Name('ИП Соколов Герман Маркович');

        return new SoleProprietor($id, $name);
    }
}
