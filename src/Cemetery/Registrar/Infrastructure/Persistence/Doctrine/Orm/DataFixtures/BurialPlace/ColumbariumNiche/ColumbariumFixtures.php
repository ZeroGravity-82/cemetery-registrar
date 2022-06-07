<?php

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\DataFixtures\BurialPlace\ColumbariumNiche;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ColumbariumFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(ColumbariumProvider::getColumbariumA());
        $manager->persist(ColumbariumProvider::getColumbariumB());
        $manager->persist(ColumbariumProvider::getColumbariumC());
        $manager->persist(ColumbariumProvider::getColumbariumD());
        $manager->flush();
    }
}
