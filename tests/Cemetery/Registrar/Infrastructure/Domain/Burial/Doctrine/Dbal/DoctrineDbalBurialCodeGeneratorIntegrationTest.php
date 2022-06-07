<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Burial\Doctrine\Dbal;

use Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\Dbal\DoctrineDbalBurialCodeGenerator;
use Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\Orm\DoctrineOrmBurialRepository;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\DataFixtures\Burial\BurialProvider;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalBurialCodeGeneratorIntegrationTest extends KernelTestCase
{
    private DoctrineDbalBurialCodeGenerator $generator;
    private DoctrineOrmBurialRepository     $burialRepo;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        /** @var Connection $connection */
        $connection = $container->get(Connection::class);
        $this->generator = new DoctrineDbalBurialCodeGenerator($connection);

        /** @var EntityManagerInterface $entityManager */
        $entityManager    = $container->get(EntityManagerInterface::class);
        $this->burialRepo = new DoctrineOrmBurialRepository($entityManager);
        $this->truncateEntities($entityManager);
    }

    public function testItReturnsCodeForEmptyTable(): void
    {
        $this->assertSame('1', $this->generator->getNextCode());
    }

    public function testItReturnsCodeRelevantToTableContent(): void
    {
        $this->burialRepo->save(BurialProvider::getBurialA());
        $this->assertSame('12', $this->generator->getNextCode());

        $this->burialRepo->save(BurialProvider::getBurialB());
        $this->assertSame('11003', $this->generator->getNextCode());

        $this->burialRepo->save(BurialProvider::getBurialC());
        $this->assertSame('11004', $this->generator->getNextCode());

        $this->burialRepo->save(BurialProvider::getBurialD());
        $this->assertSame('234117891', $this->generator->getNextCode());

        $this->burialRepo->save(BurialProvider::getBurialE());
        $this->assertSame('234117891', $this->generator->getNextCode());

        $this->burialRepo->save(BurialProvider::getBurialF());
        $this->assertSame('234117891', $this->generator->getNextCode());

        $this->burialRepo->save(BurialProvider::getBurialG());
        $this->assertSame('234117891', $this->generator->getNextCode());
    }

    private function truncateEntities(EntityManagerInterface $entityManager): void
    {
        (new OrmPurger($entityManager))->purge();
    }
}
