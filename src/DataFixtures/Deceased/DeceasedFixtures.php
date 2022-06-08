<?php

namespace DataFixtures\Deceased;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DeceasedFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(DeceasedProvider::getDeceasedA());
        $manager->persist(DeceasedProvider::getDeceasedB());
        $manager->persist(DeceasedProvider::getDeceasedC());
        $manager->persist(DeceasedProvider::getDeceasedD());
        $manager->persist(DeceasedProvider::getDeceasedE());
        $manager->persist(DeceasedProvider::getDeceasedF());
        $manager->persist(DeceasedProvider::getDeceasedG());
        $manager->flush();
    }
}
