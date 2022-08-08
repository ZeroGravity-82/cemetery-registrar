<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockName;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRemoved;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockRemovedTest extends EventTest
{
    private CemeteryBlockId   $cemeteryBlockId;
    private CemeteryBlockName $cemeteryBlockName;

    public function setUp(): void
    {
        $this->cemeteryBlockId   = new CemeteryBlockId('CB001');
        $this->cemeteryBlockName = new CemeteryBlockName('южный');
        $this->event             = new CemeteryBlockRemoved(
            $this->cemeteryBlockId,
            $this->cemeteryBlockName,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->cemeteryBlockId->isEqual($this->event->cemeteryBlockId()));
        $this->assertTrue($this->cemeteryBlockName->isEqual($this->event->cemeteryBlockName()));
    }
}
