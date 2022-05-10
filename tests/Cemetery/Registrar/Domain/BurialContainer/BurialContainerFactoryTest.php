<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialContainer;

use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialContainer\BurialContainerFactory;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialContainerFactoryTest extends TestCase
{
    public function testItCreatesBurialContainer(): void
    {
        $burialContainerFactory = new BurialContainerFactory();

        $coffinSize      = 180;
        $coffinShape     = CoffinShape::GREEK_WITH_HANDLES;
        $burialContainer = $burialContainerFactory->createForCoffin($coffinSize, $coffinShape, true);
        $this->assertInstanceOf(BurialContainer::class, $burialContainer);
        $this->assertInstanceOf(Coffin::class, $burialContainer->container());
        $this->assertSame($coffinSize, $burialContainer->container()->size()->value());
        $this->assertSame($coffinShape, $burialContainer->container()->shape()->value());
        $this->assertTrue($burialContainer->container()->isNonStandard());

        $burialContainer = $burialContainerFactory->createForUrn();
        $this->assertInstanceOf(BurialContainer::class, $burialContainer);
        $this->assertInstanceOf(Urn::class, $burialContainer->container());
    }
}
