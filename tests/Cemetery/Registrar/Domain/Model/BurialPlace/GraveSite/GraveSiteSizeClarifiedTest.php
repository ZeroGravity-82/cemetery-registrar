<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSizeClarified;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteSizeClarifiedTest extends EventTest
{
    private GraveSiteId   $graveSiteId;
    private GraveSiteSize $size;

    public function setUp(): void
    {
        $this->graveSiteId = new GraveSiteId('GS001');
        $this->size        = new GraveSiteSize('2.5');
        $this->event       = new GraveSiteSizeClarified(
            $this->graveSiteId,
            $this->size,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->graveSiteId->isEqual($this->event->graveSiteId()));
        $this->assertTrue($this->size->isEqual($this->event->size()));
    }
}
