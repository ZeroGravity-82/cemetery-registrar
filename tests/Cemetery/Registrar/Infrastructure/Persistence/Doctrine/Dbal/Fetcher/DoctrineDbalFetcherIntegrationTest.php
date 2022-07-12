<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\Fetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalFetcher;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class DoctrineDbalFetcherIntegrationTest extends KernelTestCase
{
    protected const DEFAULT_PAGE_SIZE = 20;

    protected AbstractDatabaseTool   $databaseTool;
    protected Connection             $connection;
    protected EntityManagerInterface $entityManager;
    protected DoctrineDbalFetcher    $fetcher;

    abstract protected function loadFixtures();

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

    public function testItHasValidPageSizeConstant(): void
    {
        $this->assertSame(self::DEFAULT_PAGE_SIZE, Fetcher::DEFAULT_PAGE_SIZE);
    }

    public function testItReturnsNullIfNoDataFoundById(): void
    {
        $view = $this->fetcher->findViewById('unknown_id');
        $this->assertNull($view);
    }

    protected function assertValidDateTimeValue(string $value): void
    {
        $this->assertTrue(
            new \DateTimeImmutable() >= \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value)
        );
    }
}
