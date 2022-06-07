<?php

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\DataFixtures\FuneralCompany;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FuneralCompanyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(FuneralCompanyProvider::getFuneralCompanyA());
        $manager->persist(FuneralCompanyProvider::getFuneralCompanyB());
        $manager->persist(FuneralCompanyProvider::getFuneralCompanyC());
        $manager->persist(FuneralCompanyProvider::getFuneralCompanyD());
        $manager->flush();
    }
}
