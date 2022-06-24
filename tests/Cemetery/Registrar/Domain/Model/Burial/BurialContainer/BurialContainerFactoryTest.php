<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Burial\BurialContainer;

use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\BurialContainerFactory;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Urn;
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
