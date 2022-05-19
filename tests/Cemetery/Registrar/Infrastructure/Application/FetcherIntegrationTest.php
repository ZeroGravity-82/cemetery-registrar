<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Application;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class FetcherIntegrationTest extends KernelTestCase
{
    protected Connection             $connection;
    protected EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        /** @var Connection $connection */
        $connection       = $container->get(Connection::class);
        $this->connection = $connection;

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
