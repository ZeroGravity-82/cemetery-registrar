<?php

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\DataFixtures\BurialPlace\GraveSite;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GraveSiteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(GraveSiteProvider::getGraveSiteA());
        $manager->persist(GraveSiteProvider::getGraveSiteB());
        $manager->persist(GraveSiteProvider::getGraveSiteC());
        $manager->persist(GraveSiteProvider::getGraveSiteD());
        $manager->persist(GraveSiteProvider::getGraveSiteE());
        $manager->flush();
    }
}
