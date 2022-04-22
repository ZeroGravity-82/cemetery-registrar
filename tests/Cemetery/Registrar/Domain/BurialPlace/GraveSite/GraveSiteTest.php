<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\CemeteryBlockId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\RowInBlock;
use Cemetery\Registrar\Domain\Geolocation\Accuracy;
use Cemetery\Registrar\Domain\Geolocation\Coordinates;
use Cemetery\Registrar\Domain\Geolocation\Position;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteTest extends TestCase
{
    public function testItHasValidLabelConstant(): void
    {
        $this->assertSame('могила', GraveSite::LABEL);
    }

    public function testItSuccessfullyCreated(): void
    {
        $id              = new GraveSiteId('GS001');
        $cemeteryBlockId = new CemeteryBlockId('CB001');
        $rowInBlock      = new RowInBlock('RIB001');
        $position        = new Position(new Coordinates('54.9472658', '82.8043771'), new Accuracy('0.25'));
        $size            = new GraveSiteSize('2.5');
        $graveSite       = new GraveSite($id, $cemeteryBlockId, $rowInBlock, $position, $size);
    }

    public function testItFailsWithEmptyValue(): void
    {

    }

}
