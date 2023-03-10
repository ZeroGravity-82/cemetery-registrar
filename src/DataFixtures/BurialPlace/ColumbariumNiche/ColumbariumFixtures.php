<?php

declare(strict_types=1);

namespace DataFixtures\BurialPlace\ColumbariumNiche;

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
