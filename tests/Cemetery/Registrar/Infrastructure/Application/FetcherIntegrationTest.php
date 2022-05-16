<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Application;

use Cemetery\Registrar\Domain\Entity;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class FetcherIntegrationTest extends KernelTestCase
{
    protected Entity                 $entityA;
    protected Entity                 $entityB;
    protected Entity                 $entityC;
    protected EntityManagerInterface $entityManager;
    protected object                 $repo;

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
