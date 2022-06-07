<?php

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\DataFixtures\Organization\JuristicPerson;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JuristicPersonFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(JuristicPersonProvider::getJuristicPersonA());
        $manager->persist(JuristicPersonProvider::getJuristicPersonB());
        $manager->persist(JuristicPersonProvider::getJuristicPersonC());
        $manager->persist(JuristicPersonProvider::getJuristicPersonD());
        $manager->persist(JuristicPersonProvider::getJuristicPersonE());
        $manager->flush();
    }
}