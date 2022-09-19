<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorRemoved;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorRemovedTest extends EventTest
{
    private SoleProprietorId $id;

    public function setUp(): void
    {
        $this->id    = new SoleProprietorId('888');
        $this->event = new SoleProprietorRemoved(
            $this->id,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->id->isEqual($this->event->id()));
    }
}
