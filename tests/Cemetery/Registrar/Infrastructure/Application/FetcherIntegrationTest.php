<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Application;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class FetcherIntegrationTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        /** @var EntityManagerInterface $entityManager */
        $entityManager       = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
        $this->truncateEntities();
    }

    protected function truncateEntities(): void
    {
        (new OrmPurger($this->entityManager))->purge();
    }
}
