<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathFactory;
use Cemetery\Tests\Registrar\Domain\Model\AbstractEntityFactoryTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathFactoryTest extends AbstractEntityFactoryTest
{
    private CauseOfDeathFactory $causeOfDeathFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->causeOfDeathFactory = new CauseOfDeathFactory($this->mockIdentityGenerator);
    }

    public function testItCreatesCauseOfDeath(): void
    {
        $name = 'Некоторая причина смерти';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $causeOfDeath = $this->causeOfDeathFactory->create(
            $name,
        );
        $this->assertInstanceOf(CauseOfDeath::class, $causeOfDeath);
        $this->assertSame(self::ENTITY_ID, $causeOfDeath->id()->value());
        $this->assertSame($name, $causeOfDeath->name()->value());
    }
}
