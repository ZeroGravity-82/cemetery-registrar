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
    private CemeteryBlockId   $cemeteryBlockId;

    public function setUp(): void
    {
        $this->cemeteryBlockId = new CemeteryBlockId('CB001');
        $this->event           = new CemeteryBlockRemoved(
            $this->cemeteryBlockId,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->cemeteryBlockId->isEqual($this->event->cemeteryBlockId()));
    }
}
