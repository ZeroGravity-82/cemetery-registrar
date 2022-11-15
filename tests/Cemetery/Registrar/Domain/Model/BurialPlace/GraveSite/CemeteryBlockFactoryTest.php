<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlock;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockFactory;
use Cemetery\Tests\Registrar\Domain\Model\AbstractEntityFactoryTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockFactoryTest extends AbstractEntityFactoryTest
{
    private CemeteryBlockFactory $cemeteryBlockFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->cemeteryBlockFactory = new CemeteryBlockFactory($this->mockIdentityGenerator);
    }

    public function testItCreatesCemeteryBlock(): void
    {
        $name = 'южный';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $cemeteryBlock = $this->cemeteryBlockFactory->create(
            $name,
        );
        $this->assertInstanceOf(CemeteryBlock::class, $cemeteryBlock);
        $this->assertSame(self::ENTITY_ID, $cemeteryBlock->id()->value());
        $this->assertSame($name, $cemeteryBlock->name()->value());
    }
}
