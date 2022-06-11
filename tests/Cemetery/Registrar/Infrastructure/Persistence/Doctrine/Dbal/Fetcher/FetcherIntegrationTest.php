<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class FetcherIntegrationTest extends KernelTestCase
{
    protected AbstractDatabaseTool   $databaseTool;
    protected Connection             $connection;
    protected EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        /** @var DatabaseToolCollection $databaseToolCollection */
        $databaseToolCollection = $container->get(DatabaseToolCollection::class);
        $this->databaseTool     = $databaseToolCollection->get();

        /** @var Connection $connection */
        $connection       = $container->get(Connection::class);
        $this->connection = $connection;

        /** @var EntityManagerInterface $entityManager */
        $entityManager       = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
    }

    abstract protected function loadFixtures();
}
