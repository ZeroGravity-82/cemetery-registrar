<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyCollection;
use Cemetery\Tests\Registrar\Domain\AbstractEntityCollectionTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyCollectionTest extends AbstractEntityCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = FuneralCompanyProvider::getFuneralCompanyA();
        $this->entityB    = FuneralCompanyProvider::getFuneralCompanyB();
        $this->entityC    = FuneralCompanyProvider::getFuneralCompanyC();
        $this->entityD    = FuneralCompanyProvider::getFuneralCompanyD();
        $this->idA        = $this->entityA->getId();
        $this->idB        = $this->entityB->getId();
        $this->idC        = $this->entityC->getId();
        $this->idD        = $this->entityD->getId();
        $this->collection = new FuneralCompanyCollection([$this->entityA]);
    }

    public function testItReturnsEntityClass(): void
    {
        $this->assertSame(FuneralCompany::class, $this->collection->getSupportedEntityClass());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (FuneralCompany $funeralCompany) {
            return $funeralCompany->getNote() !== null;
        };
    }
}
