<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\Deceased\CauseOfDeath;
use Cemetery\Registrar\Domain\Deceased\DeathCertificateId;
use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;

final class DeceasedProvider
{
    public static function getDeceasedA(): Deceased
    {
        $id              = new DeceasedId('D001');
        $naturalPersonId = new NaturalPersonId('NP001');
        $diedAt          = new \DateTimeImmutable('2021-12-01');

        return new Deceased($id, $naturalPersonId, $diedAt, null, null);
    }

    public static function getDeceasedB(): Deceased
    {
        $id                 = new DeceasedId('D002');
        $naturalPersonId    = new NaturalPersonId('NP002');
        $diedAt             = new \DateTimeImmutable('2001-02-11');
        $deathCertificateId = new DeathCertificateId('DC001');
        $causeOfDeath       = new CauseOfDeath('Some cause 1');

        return new Deceased($id, $naturalPersonId, $diedAt, $deathCertificateId, $causeOfDeath);
    }

    public static function getDeceasedC(): Deceased
    {
        $id                 = new DeceasedId('D003');
        $naturalPersonId    = new NaturalPersonId('NP003');
        $diedAt             = new \DateTimeImmutable('2011-05-13');
        $deathCertificateId = new DeathCertificateId('DC002');

        return new Deceased($id, $naturalPersonId, $diedAt, $deathCertificateId, null);
    }

    public static function getDeceasedD(): Deceased
    {
        $id                 = new DeceasedId('D004');
        $naturalPersonId    = new NaturalPersonId('NP004');
        $diedAt             = new \DateTimeImmutable('2015-03-10');

        return new Deceased($id, $naturalPersonId, $diedAt, null, null);
    }
}
