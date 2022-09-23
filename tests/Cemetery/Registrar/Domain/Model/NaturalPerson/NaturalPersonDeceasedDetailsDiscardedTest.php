<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonDeceasedDetailsDiscarded;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonDeceasedDetailsDiscardedTest extends EventTest
{
    private NaturalPersonId $id;

    public function setUp(): void
    {
        $this->id    = new NaturalPersonId('NP001');
        $this->event = new NaturalPersonDeceasedDetailsDiscarded(
            $this->id,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->id->isEqual($this->event->id()));
    }
}
