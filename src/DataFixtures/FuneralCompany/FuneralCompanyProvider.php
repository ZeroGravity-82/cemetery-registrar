<?php

declare(strict_types=1);

namespace DataFixtures\FuneralCompany;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyName;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyNote;

class FuneralCompanyProvider
{
    public static function getFuneralCompanyA(): FuneralCompany
    {
        $id   = new FuneralCompanyId('FC001');
        $name = new FuneralCompanyName('Апостол');

        return new FuneralCompany($id, $name);
    }

    public static function getFuneralCompanyB(): FuneralCompany
    {
        $id   = new FuneralCompanyId('FC002');
        $name = new FuneralCompanyName('Мемориал');
        $note = new FuneralCompanyNote('Фирма расположена в Кемерове');

        return (new FuneralCompany($id, $name))
            ->setNote($note);
    }

    public static function getFuneralCompanyC(): FuneralCompany
    {
        $id   = new FuneralCompanyId('FC003');
        $name = new FuneralCompanyName('Городская ритуальная служба');
        $note = new FuneralCompanyNote('Покрышкина 29, +7(383)388-85-90');

        return (new FuneralCompany($id, $name))
            ->setNote($note);
    }

    public static function getFuneralCompanyD(): FuneralCompany
    {
        $id   = new FuneralCompanyId('FC004');
        $name = new FuneralCompanyName('Похоронный Дом "Некрополь"');

        return new FuneralCompany($id, $name);
    }
}
