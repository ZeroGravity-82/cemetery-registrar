<?php

declare(strict_types=1);

namespace DataFixtures\Burial;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BurialFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(BurialProvider::getBurialA());
        $manager->persist(BurialProvider::getBurialB());
        $manager->persist(BurialProvider::getBurialC());
        $manager->persist(BurialProvider::getBurialD());
        $manager->persist(BurialProvider::getBurialE());
        $manager->persist(BurialProvider::getBurialF());
        $manager->persist(BurialProvider::getBurialG());
        $manager->flush();
    }
}
