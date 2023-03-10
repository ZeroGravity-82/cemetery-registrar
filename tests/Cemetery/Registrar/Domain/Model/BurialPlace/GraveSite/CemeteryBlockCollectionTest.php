<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlock;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockCollection;
use Cemetery\Tests\Registrar\Domain\Model\AbstractEntityCollectionTest;
use DataFixtures\BurialPlace\GraveSite\CemeteryBlockProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockCollectionTest extends AbstractEntityCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = CemeteryBlockProvider::getCemeteryBlockA();
        $this->entityB    = CemeteryBlockProvider::getCemeteryBlockB();
        $this->entityC    = CemeteryBlockProvider::getCemeteryBlockC();
        $this->entityD    = CemeteryBlockProvider::getCemeteryBlockD();
        $this->idA        = $this->entityA->id();
        $this->idB        = $this->entityB->id();
        $this->idC        = $this->entityC->id();
        $this->idD        = $this->entityD->id();
        $this->collection = new CemeteryBlockCollection([$this->entityA]);
    }

    public function testItReturnsSupportedEntityClassName(): void
    {
        $this->assertSame(CemeteryBlock::class, $this->collection->supportedEntityClassName());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (CemeteryBlock $cemeteryBlock) {
            return \str_contains($cemeteryBlock->name()->value(), 'общий');
        };
    }
}
