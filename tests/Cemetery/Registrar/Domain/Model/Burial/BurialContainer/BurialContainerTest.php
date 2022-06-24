<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Burial\BurialContainer;

use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Urn;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialContainerTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $burialContainer = new BurialContainer(new Coffin(new CoffinSize(180), CoffinShape::american(), false));
        $this->assertInstanceOf(Coffin::class, $burialContainer->container());
        $this->assertSame(180, $burialContainer->container()->size()->value());
        $this->assertTrue($burialContainer->container()->shape()->isAmerican());
        $this->assertFalse($burialContainer->container()->isNonStandard());

        $burialContainer = new BurialContainer(new Urn());
        $this->assertInstanceOf(Urn::class, $burialContainer->container());
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
