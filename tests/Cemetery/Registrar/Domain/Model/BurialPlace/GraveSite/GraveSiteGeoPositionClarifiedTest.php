<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteGeoPositionClarified;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteGeoPositionClarifiedTest extends EventTest
{
    private GraveSiteId $graveSiteId;
    private GeoPosition $geoPosition;

    public function setUp(): void
    {
        $this->graveSiteId = new GraveSiteId('GS001');
        $this->geoPosition = new GeoPosition(
            new Coordinates('55.0293096', '82.9659138'),
            new Error('0.25'),
        );
        $this->event = new GraveSiteGeoPositionClarified(
            $this->graveSiteId,
            $this->geoPosition,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->graveSiteId->isEqual($this->event->graveSiteId()));
        $this->assertTrue($this->geoPosition->isEqual($this->event->geoPosition()));
    }
}
