<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRemoved;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockRemovedTest extends EventTest
{
    private CemeteryBlockId $id;

    public function setUp(): void
    {
        $this->id    = new CemeteryBlockId('CB001');
        $this->event = new CemeteryBlockRemoved(
            $this->id,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->id->isEqual($this->event->id()));
    }
}
