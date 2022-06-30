<?php

declare(strict_types=1);

namespace DataFixtures\Deceased;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\Deceased\Age;
use Cemetery\Registrar\Domain\Model\Deceased\DeathCertificateId;
use Cemetery\Registrar\Domain\Model\Deceased\Deceased;
use Cemetery\Registrar\Domain\Model\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedProvider
{
    public static function getDeceasedA(): Deceased
    {
        $id              = new DeceasedId('D001');
        $naturalPersonId = new NaturalPersonId('NP001');
        $diedAt          = new \DateTimeImmutable('2021-12-01');

        return new Deceased($id, $naturalPersonId, $diedAt);
    }

    public static function getDeceasedB(): Deceased
    {
        $id                 = new DeceasedId('D002');
        $naturalPersonId    = new NaturalPersonId('NP002');
        $diedAt             = new \DateTimeImmutable('2001-02-12');
        $age                = new Age(82);
        $deathCertificateId = new DeathCertificateId('DC001');
        $causeOfDeath       = new CauseOfDeathId('CD008');

        return (new Deceased($id, $naturalPersonId, $diedAt))
            ->setAge($age)
            ->setDeathCertificateId($deathCertificateId)
            ->setCauseOfDeathId($causeOfDeath);
    }

    public static function getDeceasedC(): Deceased
    {
        $id                 = new DeceasedId('D003');
        $naturalPersonId    = new NaturalPersonId('NP003');
        $diedAt             = new \DateTimeImmutable('2012-05-13');
        $deathCertificateId = new DeathCertificateId('DC002');
        $causeOfDeath       = new CauseOfDeathId('CD004');

        return (new Deceased($id, $naturalPersonId, $diedAt))
            ->setDeathCertificateId($deathCertificateId)
            ->setCauseOfDeathId($causeOfDeath);
    }

    public static function getDeceasedD(): Deceased
    {
        $id              = new DeceasedId('D004');
        $naturalPersonId = new NaturalPersonId('NP005');
        $diedAt          = new \DateTimeImmutable('2022-03-10');

        return new Deceased($id, $naturalPersonId, $diedAt);
    }

    public static function getDeceasedE(): Deceased
    {
        $id              = new DeceasedId('D005');
        $naturalPersonId = new NaturalPersonId('NP004');
        $diedAt          = new \DateTimeImmutable('2010-01-26');

        return new Deceased($id, $naturalPersonId, $diedAt);
    }

    public static function getDeceasedF(): Deceased
    {
        $id              = new DeceasedId('D006');
        $naturalPersonId = new NaturalPersonId('NP006');
        $diedAt          = new \DateTimeImmutable('2021-12-03');

        return new Deceased($id, $naturalPersonId, $diedAt);
    }

    public static function getDeceasedG(): Deceased
    {
        $id              = new DeceasedId('D007');
        $naturalPersonId = new NaturalPersonId('NP009');
        $diedAt          = new \DateTimeImmutable('1980-05-26');

        return new Deceased($id, $naturalPersonId, $diedAt);
    }
}
