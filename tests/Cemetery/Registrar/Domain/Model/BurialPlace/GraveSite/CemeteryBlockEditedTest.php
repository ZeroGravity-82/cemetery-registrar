<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockEdited;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockName;
use Cemetery\Tests\Registrar\Domain\Model\AbstractEventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockEditedTest extends AbstractEventTest
{
    private CemeteryBlockId   $id;
    private CemeteryBlockName $name;

    public function setUp(): void
    {
        $this->id    = new CemeteryBlockId('CB001');
        $this->name  = new CemeteryBlockName('южный');
        $this->event = new CemeteryBlockEdited(
            $this->id,
            $this->name,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->name->isEqual($this->event->name()));
    }
}
