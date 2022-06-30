<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathFactory;
use Cemetery\Tests\Registrar\Domain\Model\EntityFactoryTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathFactoryTest extends EntityFactoryTest
{
    private CauseOfDeathFactory $deceasedFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->deceasedFactory = new CauseOfDeathFactory($this->mockIdentityGenerator);
    }

    public function testItCreatesCauseOfDeath(): void
    {
        $name = 'Некоторая причина смерти';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $causeOfDeath = $this->deceasedFactory->create(
            $name,
        );
        $this->assertInstanceOf(CauseOfDeath::class, $causeOfDeath);
        $this->assertSame(self::ENTITY_ID, $causeOfDeath->id()->value());
        $this->assertSame($name, $causeOfDeath->name()->value());
    }
}
