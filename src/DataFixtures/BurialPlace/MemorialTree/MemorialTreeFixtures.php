<?php

declare(strict_types=1);

namespace DataFixtures\BurialPlace\MemorialTree;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MemorialTreeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(MemorialTreeProvider::getMemorialTreeA());
        $manager->persist(MemorialTreeProvider::getMemorialTreeB());
        $manager->persist(MemorialTreeProvider::getMemorialTreeC());
        $manager->persist(MemorialTreeProvider::getMemorialTreeD());
        $manager->flush();
    }
}
