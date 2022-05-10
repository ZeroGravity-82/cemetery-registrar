<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeFactory;
use Cemetery\Tests\Registrar\Domain\EntityFactoryTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeFactoryTest extends EntityFactoryTest
{
    private MemorialTreeFactory $memorialTreeFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->memorialTreeFactory = new MemorialTreeFactory($this->mockIdentityGenerator);
    }

    public function testItCreatesColumbariumNiche(): void
    {
        $treeNumber           = '001';
        $geoPositionLatitude  = '54.950357';
        $geoPositionLongitude = '82.7972252';
        $geoPositionError     = '0.2';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $memorialTree = $this->memorialTreeFactory->create(
            $treeNumber,
            $geoPositionLatitude,
            $geoPositionLongitude,
            $geoPositionError,
        );
        $this->assertInstanceOf(MemorialTree::class, $memorialTree);
        $this->assertSame(self::ENTITY_ID, $memorialTree->id()->value());
        $this->assertSame($treeNumber, $memorialTree->treeNumber()->value());
        $this->assertSame($geoPositionLatitude, $memorialTree->geoPosition()->coordinates()->latitude());
        $this->assertSame($geoPositionLongitude, $memorialTree->geoPosition()->coordinates()->longitude());
        $this->assertSame($geoPositionError, $memorialTree->geoPosition()->error()->value());
    }

    public function testItCreatesColumbariumNicheWithoutOptionalFields(): void
    {
        $treeNumber = '001';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $memorialTree = $this->memorialTreeFactory->create(
            $treeNumber,
            null,
            null,
            null,
        );
        $this->assertInstanceOf(MemorialTree::class, $memorialTree);
        $this->assertSame(self::ENTITY_ID, $memorialTree->id()->value());
        $this->assertSame($treeNumber, $memorialTree->treeNumber()->value());
        $this->assertNull($memorialTree->geoPosition());
    }
}
