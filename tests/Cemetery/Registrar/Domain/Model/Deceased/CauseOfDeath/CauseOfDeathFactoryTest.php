<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathDescription;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathFactory;
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
        $description = 'Некоторая причина смерти';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $causeOfDeath = $this->deceasedFactory->create(
            $description,
        );
        $this->assertInstanceOf(CauseOfDeath::class, $causeOfDeath);
        $this->assertSame(self::ENTITY_ID, $causeOfDeath->id()->value());
        $this->assertSame($description, $causeOfDeath->description()->value());
    }
}
