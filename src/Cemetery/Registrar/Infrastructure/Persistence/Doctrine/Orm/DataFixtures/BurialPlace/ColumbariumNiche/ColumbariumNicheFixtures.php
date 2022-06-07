<?php

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\DataFixtures\BurialPlace\ColumbariumNiche;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ColumbariumNicheFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(ColumbariumNicheProvider::getColumbariumNicheA());
        $manager->persist(ColumbariumNicheProvider::getColumbariumNicheB());
        $manager->persist(ColumbariumNicheProvider::getColumbariumNicheC());
        $manager->persist(ColumbariumNicheProvider::getColumbariumNicheD());
        $manager->flush();
    }
}