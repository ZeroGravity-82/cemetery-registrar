<?php

namespace DataFixtures\Deceased\CauseOfDeath;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CauseOfDeathFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(CauseOfDeathProvider::getCauseOfDeathA());
        $manager->persist(CauseOfDeathProvider::getCauseOfDeathB());
        $manager->persist(CauseOfDeathProvider::getCauseOfDeathC());
        $manager->persist(CauseOfDeathProvider::getCauseOfDeathD());
        $manager->persist(CauseOfDeathProvider::getCauseOfDeathE());
        $manager->persist(CauseOfDeathProvider::getCauseOfDeathF());
        $manager->persist(CauseOfDeathProvider::getCauseOfDeathG());
        $manager->persist(CauseOfDeathProvider::getCauseOfDeathH());
        $manager->flush();
    }
}
