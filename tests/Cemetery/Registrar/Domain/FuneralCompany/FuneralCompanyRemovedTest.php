<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRemoved;
use Cemetery\Tests\Registrar\Domain\AbstractEventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyRemovedTest extends AbstractEventTest
{
    private FuneralCompanyId $funeralCompanyId;

    public function setUp(): void
    {
        $this->funeralCompanyId = new FuneralCompanyId('888');
        $this->event            = new FuneralCompanyRemoved($this->funeralCompanyId);
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertSame($this->funeralCompanyId, $this->event->getFuneralCompanyId());
    }
}
