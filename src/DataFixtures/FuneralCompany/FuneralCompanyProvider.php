<?php

declare(strict_types=1);

namespace DataFixtures\FuneralCompany;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyNote;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;

class FuneralCompanyProvider
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
