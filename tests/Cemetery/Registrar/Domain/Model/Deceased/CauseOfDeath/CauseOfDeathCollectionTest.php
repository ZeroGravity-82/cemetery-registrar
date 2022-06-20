<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathCollection;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootCollectionTest;
use DataFixtures\Deceased\CauseOfDeath\CauseOfDeathProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathCollectionTest extends AggregateRootCollectionTest
{
    public function setUp(): void
    {
        $this->entityA    = CauseOfDeathProvider::getCauseOfDeathA();
        $this->entityB    = CauseOfDeathProvider::getCauseOfDeathB();
        $this->entityC    = CauseOfDeathProvider::getCauseOfDeathC();
        $this->entityD    = CauseOfDeathProvider::getCauseOfDeathD();
        $this->idA        = $this->entityA->id();
        $this->idB        = $this->entityB->id();
        $this->idC        = $this->entityC->id();
        $this->idD        = $this->entityD->id();
        $this->collection = new CauseOfDeathCollection([$this->entityA]);
    }

    public function testItReturnsSupportedClassName(): void
    {
        $this->assertSame(CauseOfDeath::class, $this->collection->supportedClassName());
    }

    protected function getClosureForCollectionFiltering(): \Closure
    {
        return function (CauseOfDeath $causeOfDeath) {
            return \str_contains($causeOfDeath->name()->value(), 'болезнь');
        };
    }
}
