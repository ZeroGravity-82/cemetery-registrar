<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockCreated;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockName;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockCreatedTest extends EventTest
{
    private CemeteryBlockId   $cemeteryBlockId;
    private CemeteryBlockName $cemeteryBlockName;

    public function setUp(): void
    {
        $this->cemeteryBlockId   = new CemeteryBlockId('CD001');
        $this->cemeteryBlockName = new CemeteryBlockName('Асфиксия');
        $this->event             = new CemeteryBlockCreated(
            $this->cemeteryBlockId,
            $this->cemeteryBlockName,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertSame($this->cemeteryBlockId, $this->event->cemeteryBlockId());
        $this->assertSame($this->cemeteryBlockName, $this->event->cemeteryBlockName());
    }
}
