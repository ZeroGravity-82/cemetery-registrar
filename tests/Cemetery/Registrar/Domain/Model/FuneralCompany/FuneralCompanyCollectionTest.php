<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\FuneralCompany;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyCollection;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootCollectionTest;
use DataFixtures\FuneralCompany\FuneralCompanyProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyCollectionTest extends AggregateRootCollectionTest
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

    public function testItReturnsSupportedEntityClassName(): void
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
