<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialContainer;

use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialContainerTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $coffin          = new Coffin(new CoffinSize(180), CoffinShape::american(), false);
        $burialContainer = new BurialContainer($coffin);
        $this->assertTrue($burialContainer->container()->isEqual($coffin));

        $urn             = new Urn();
        $burialContainer = new BurialContainer($urn);
        $this->assertTrue($burialContainer->container()->isEqual($urn));
    }

    public function testItComparable(): void
    {
        $coffinA          = new Coffin(new CoffinSize(180), CoffinShape::american(), false);
        $coffinB          = new Coffin(new CoffinSize(175), CoffinShape::greekWithHandles(), true);
        $urn              = new Urn();
        $burialContainerA = new BurialContainer($coffinA);
        $burialContainerB = new BurialContainer($coffinB);
        $burialContainerC = new BurialContainer($urn);
        $burialContainerD = new BurialContainer($coffinA);
        $this->assertFalse($burialContainerA->isEqual($burialContainerB));
        $this->assertFalse($burialContainerA->isEqual($burialContainerC));
        $this->assertTrue($burialContainerA->isEqual($burialContainerD));
        $this->assertFalse($burialContainerB->isEqual($burialContainerC));
        $this->assertFalse($burialContainerB->isEqual($burialContainerD));
        $this->assertFalse($burialContainerC->isEqual($burialContainerD));
    }
}
