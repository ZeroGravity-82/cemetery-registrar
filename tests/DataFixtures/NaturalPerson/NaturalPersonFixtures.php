<?php

namespace DataFixtures\NaturalPerson;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NaturalPersonFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(NaturalPersonProvider::getNaturalPersonA());
        $manager->persist(NaturalPersonProvider::getNaturalPersonB());
        $manager->persist(NaturalPersonProvider::getNaturalPersonC());
        $manager->persist(NaturalPersonProvider::getNaturalPersonD());
        $manager->persist(NaturalPersonProvider::getNaturalPersonE());
        $manager->persist(NaturalPersonProvider::getNaturalPersonF());
        $manager->persist(NaturalPersonProvider::getNaturalPersonG());
        $manager->persist(NaturalPersonProvider::getNaturalPersonH());
        $manager->persist(NaturalPersonProvider::getNaturalPersonI());
        $manager->flush();
    }
}
