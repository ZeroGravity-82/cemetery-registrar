<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedFactory;
use Cemetery\Tests\Registrar\Domain\EntityFactoryTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedFactoryTest extends EntityFactoryTest
{
    private DeceasedFactory $deceasedFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->deceasedFactory = new DeceasedFactory($this->mockIdentityGenerator);
    }

    public function testItCreatesDeceased(): void
    {
        $naturalPersonId    = 'NP001';
        $diedAt             = '2021-04-23';
        $age                = 82;
        $deathCertificateId = 'DC001';
        $causeOfDeath       = 'Некоторая причина смерти';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $deceased = $this->deceasedFactory->create(
            $naturalPersonId,
            $diedAt,
            $age,
            $deathCertificateId,
            $causeOfDeath,
        );
        $this->assertInstanceOf(Deceased::class, $deceased);
        $this->assertSame(self::ENTITY_ID, $deceased->id()->value());
        $this->assertSame($naturalPersonId, $deceased->naturalPersonId()->value());
        $this->assertSame($diedAt, $deceased->diedAt()->format('Y-m-d'));
        $this->assertSame($age, $deceased->age()->value());
        $this->assertSame($deathCertificateId, $deceased->deathCertificateId()->value());
        $this->assertSame($causeOfDeath, $deceased->causeOfDeath()->value());
    }

    public function testItCreatesDeceasedWithoutOptionalFields(): void
    {
        $naturalPersonId = 'NP001';
        $diedAt          = '2021-04-23';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $deceased = $this->deceasedFactory->create(
            $naturalPersonId,
            $diedAt,
            null,
            null,
            null,
        );
        $this->assertInstanceOf(Deceased::class, $deceased);
        $this->assertSame(self::ENTITY_ID, $deceased->id()->value());
        $this->assertSame($naturalPersonId, $deceased->naturalPersonId()->value());
        $this->assertSame($diedAt, $deceased->diedAt()->format('Y-m-d'));
        $this->assertNull($deceased->age());
        $this->assertNull($deceased->deathCertificateId());
        $this->assertNull($deceased->causeOfDeath());
    }
}
