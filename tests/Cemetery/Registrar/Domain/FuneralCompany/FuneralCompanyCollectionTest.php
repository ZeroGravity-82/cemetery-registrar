<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyCollection;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\DataFixtures\FuneralCompany\FuneralCompanyProvider;
use Cemetery\Tests\Registrar\Domain\EntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyCollectionTest extends EntityCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = FuneralCompanyProvider::getFuneralCompanyA();
        $this->entityB    = FuneralCompanyProvider::getFuneralCompanyB();
        $this->entityC    = FuneralCompanyProvider::getFuneralCompanyC();
        $this->entityD    = FuneralCompanyProvider::getFuneralCompanyD();
        $this->idA        = $this->entityA->id();
        $this->idB        = $this->entityB->id();
        $this->idC        = $this->entityC->id();
        $this->idD        = $this->entityD->id();
        $this->collection = new FuneralCompanyCollection([$this->entityA]);
    }

    public function testItReturnsEntityClassName(): void
    {
        $this->assertSame(FuneralCompany::class, $this->collection->supportedEntityClassName());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (FuneralCompany $funeralCompany) {
            return $funeralCompany->note() !== null;
        };
    }
}
