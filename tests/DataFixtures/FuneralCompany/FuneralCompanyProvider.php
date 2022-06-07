<?php

declare(strict_types=1);

namespace DataFixtures\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyNote;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;

final class FuneralCompanyProvider
{
    public static function getFuneralCompanyA(): FuneralCompany
    {
        $id             = new FuneralCompanyId('FC001');
        $organizationId = new OrganizationId(new JuristicPersonId('JP001'));

        return new FuneralCompany($id, $organizationId);
    }

    public static function getFuneralCompanyB(): FuneralCompany
    {
        $id             = new FuneralCompanyId('FC002');
        $organizationId = new OrganizationId(new SoleProprietorId('SP001'));
        $note           = new FuneralCompanyNote('Фирма находится в Кемерове');

        return (new FuneralCompany($id, $organizationId))
            ->setNote($note);
    }

    public static function getFuneralCompanyC(): FuneralCompany
    {
        $id             = new FuneralCompanyId('FC003');
        $organizationId = new OrganizationId(new SoleProprietorId('SP002'));
        $note           = new FuneralCompanyNote('Примечание 2');

        return (new FuneralCompany($id, $organizationId))
            ->setNote($note);
    }

    public static function getFuneralCompanyD(): FuneralCompany
    {
        $id             = new FuneralCompanyId('FC004');
        $organizationId = new OrganizationId(new JuristicPersonId('JP002'));

        return new FuneralCompany($id, $organizationId);
    }
}
