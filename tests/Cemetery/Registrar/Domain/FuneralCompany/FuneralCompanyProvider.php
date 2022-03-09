<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Organization\OrganizationType;

final class FuneralCompanyProvider
{
    public static function getFuneralCompanyA(): FuneralCompany
    {
        $id               = new FuneralCompanyId('FC001');
        $organizationType = OrganizationType::juristicPerson();
        $organizationId   = new OrganizationId('777', $organizationType);

        return new FuneralCompany($id, $organizationId);
    }

    public static function getFuneralCompanyB(): FuneralCompany
    {
        $id               = new FuneralCompanyId('FC002');
        $organizationType = OrganizationType::soleProprietor();
        $organizationId   = new OrganizationId('888', $organizationType);

        return (new FuneralCompany($id, $organizationId))
            ->setNote('Некоторый комментарий');
    }

    public static function getFuneralCompanyC(): FuneralCompany
    {
        $id               = new FuneralCompanyId('FC003');
        $organizationType = OrganizationType::soleProprietor();
        $organizationId   = new OrganizationId('999', $organizationType);

        return (new FuneralCompany($id, $organizationId))
            ->setNote('Другой комментарий');
    }

    public static function getFuneralCompanyD(): FuneralCompany
    {
        $id               = new FuneralCompanyId('FC004');
        $organizationType = OrganizationType::juristicPerson();
        $organizationId   = new OrganizationId('AAA', $organizationType);

        return new FuneralCompany($id, $organizationId);
    }
}
