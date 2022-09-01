<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRemoved;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteRemovedTest extends EventTest
{
    private GraveSiteId $graveSiteId;

    public function setUp(): void
    {
        $this->graveSiteId     = new GraveSiteId('GS001');
        $this->event           = new GraveSiteRemoved(
            $this->graveSiteId,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->graveSiteId->isEqual($this->event->graveSiteId()));
    }
}
