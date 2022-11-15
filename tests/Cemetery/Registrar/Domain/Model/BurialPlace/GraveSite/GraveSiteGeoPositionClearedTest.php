<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteGeoPositionCleared;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Tests\Registrar\Domain\Model\AbstractEventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteGeoPositionClearedTest extends AbstractEventTest
{
    private GraveSiteId $id;

    public function setUp(): void
    {
        $this->id    = new GraveSiteId('GS001');
        $this->event = new GraveSiteGeoPositionCleared(
            $this->id,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->id->isEqual($this->event->id()));
    }
}
