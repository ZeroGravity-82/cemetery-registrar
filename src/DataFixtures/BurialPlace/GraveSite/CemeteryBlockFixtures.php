<?php

declare(strict_types=1);

namespace DataFixtures\BurialPlace\GraveSite;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CemeteryBlockFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(CemeteryBlockProvider::getCemeteryBlockA());
        $manager->persist(CemeteryBlockProvider::getCemeteryBlockB());
        $manager->persist(CemeteryBlockProvider::getCemeteryBlockC());
        $manager->persist(CemeteryBlockProvider::getCemeteryBlockD());
        $manager->flush();
    }
}
