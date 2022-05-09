<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedFactory;
use Cemetery\Registrar\Domain\IdentityGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedFactoryTest extends TestCase
{
    private MockObject|IdentityGenerator $mockIdentityGenerator;
    private DeceasedFactory              $deceasedFactory;

    public function setUp(): void
    {
        $this->mockIdentityGenerator = $this->createMock(IdentityGenerator::class);
        $this->mockIdentityGenerator->method('getNextIdentity')->willReturn('555');

        $this->deceasedFactory = new DeceasedFactory($this->mockIdentityGenerator);
    }

    public function testItCreatesDeceased(): void
    {
        $naturalPersonId    = 'NP001';
        $diedAt             = '2021-04-23';
        $deathCertificateId = 'DC001';
        $causeOfDeath       = 'Некоторая причина смерти';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $deceased = $this->deceasedFactory->create(
            $naturalPersonId,
            $diedAt,
            $deathCertificateId,
            $causeOfDeath,
        );
        $this->assertInstanceOf(Deceased::class, $deceased);
        $this->assertSame('555', $deceased->id()->value());
        $this->assertSame($naturalPersonId, $deceased->naturalPersonId()->value());
        $this->assertSame($diedAt, $deceased->diedAt()->format('Y-m-d'));
        $this->assertSame($deathCertificateId, $deceased->deathCertificateId()->value());
        $this->assertSame($causeOfDeath, $deceased->causeOfDeath()->value());
    }

    public function testItCreatesDeceasedWithoutOptionalFields(): void
    {
        $naturalPersonId = 'NP001';
        $diedAt          = '2021-04-23';
        $deceased        = $this->deceasedFactory->create(
            $naturalPersonId,
            $diedAt,
            null,
            null,
        );
        $this->assertInstanceOf(Deceased::class, $deceased);
        $this->assertSame('555', $deceased->id()->value());
        $this->assertSame($naturalPersonId, $deceased->naturalPersonId()->value());
        $this->assertSame($diedAt, $deceased->diedAt()->format('Y-m-d'));
        $this->assertNull($deceased->deathCertificateId());
        $this->assertNull($deceased->causeOfDeath());
    }

    public function testItFailsToDeceasedWithoutNaturalPersonId(): void
    {
        $this->expectExceptionForNotProvidedNaturalPersonId();
        $this->deceasedFactory->create(
            null,
            '2021-04-23',
            null,
            null,
        );
    }

    public function testItFailsToDeceasedWithoutDiedAt(): void
    {
        $this->expectExceptionForNotProvidedDiedAt();
        $this->deceasedFactory->create(
            'NP001',
            null,
            null,
            null,
        );
    }

    private function expectExceptionForNotProvidedNaturalPersonId(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Идентификатор физического лица не указан.');
    }

    private function expectExceptionForNotProvidedDiedAt(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Дата смерти не указана.');
    }
}
