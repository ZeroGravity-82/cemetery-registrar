<?php

namespace DataFixtures\Organization\SoleProprietor;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SoleProprietorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(SoleProprietorProvider::getSoleProprietorA());
        $manager->persist(SoleProprietorProvider::getSoleProprietorB());
        $manager->persist(SoleProprietorProvider::getSoleProprietorC());
        $manager->persist(SoleProprietorProvider::getSoleProprietorD());
        $manager->flush();
    }
}
