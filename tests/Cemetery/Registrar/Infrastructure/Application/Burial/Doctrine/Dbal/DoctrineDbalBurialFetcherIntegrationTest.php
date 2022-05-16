<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Application\Burial\Doctrine\Dbal;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\Orm\DoctrineOrmBurialRepository;
use Cemetery\Tests\Registrar\Domain\Burial\BurialProvider;
use Cemetery\Tests\Registrar\Infrastructure\Application\FetcherIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalBurialFetcherIntegrationTest extends FetcherIntegrationTest
{
    private Burial $entityD;
    private Burial $entityE;
    private Burial $entityF;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmBurialRepository($this->entityManager);
        $this->entityA = BurialProvider::getBurialA();
        $this->entityB = BurialProvider::getBurialB();
        $this->entityC = BurialProvider::getBurialC();
        $this->entityD = BurialProvider::getBurialD();
        $this->entityE = BurialProvider::getBurialE();
        $this->entityF = BurialProvider::getBurialF();
        $this->repo->saveAll(new BurialCollection(
            [$this->entityA, $this->entityB, $this->entityC, $this->entityD, $this->entityE, $this->entityF]
        ));
    }

    public function testItReturnsBurialViewById(): void
    {

    }

    public function testItFailsToReturnBurialViewByUnknownId(): void
    {

    }

    public function testItReturnsAllBurialViews(): void
    {

    }

    public function testItReturnsBurialViewsByPage(): void
    {

    }

    public function testItReturnsBurialViewsByTerm(): void
    {

    }

    public function testItReturnsBurialViewsByPageAndTerm(): void
    {

    }
}
