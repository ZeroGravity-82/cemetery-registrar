<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\Deceased\CauseOfDeath;
use Cemetery\Registrar\Domain\Deceased\DeathCertificateId;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\IdentityGeneratorInterface;
use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedBuilder;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedBuilderTest extends TestCase
{
    private MockObject|IdentityGeneratorInterface $mockIdentityGenerator;
    private DeceasedBuilder                       $deceasedBuilder;

    public function setUp(): void
    {
        $this->mockIdentityGenerator = $this->createMock(IdentityGeneratorInterface::class);
        $this->mockIdentityGenerator->method('getNextIdentity')->willReturn('555');

        $naturalPersonId       = '777';
        $diedAt                = new \DateTimeImmutable('2021-02-12');
        $this->deceasedBuilder = new DeceasedBuilder($this->mockIdentityGenerator);
        $this->deceasedBuilder->initialize($naturalPersonId, $diedAt);
    }

    public function testItInitializesADeceasedWithRequiredFields(): void
    {
        $deceased = $this->deceasedBuilder->build();

        $this->assertInstanceOf(Deceased::class, $deceased);
        $this->assertInstanceOf(DeceasedId::class, $deceased->id());
        $this->assertSame('555', (string) $deceased->id());
        $this->assertInstanceOf(NaturalPersonId::class, $deceased->naturalPersonId());
        $this->assertSame('777', (string) $deceased->naturalPersonId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $deceased->diedAt());
        $this->assertSame('2021-02-12', $deceased->diedAt()->format('Y-m-d'));
        $this->assertNull($deceased->deathCertificateId());
        $this->assertNull($deceased->causeOfDeath());
    }

    public function testItFailsToBuildADeceasedBeforeInitialization(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\sprintf('Строитель для класса %s не инициализирован.', Deceased::class));

        $deceasedBuilder = new DeceasedBuilder($this->mockIdentityGenerator);
        $deceasedBuilder->build();
    }

    public function testItAddsADeathCertificateId(): void
    {
        $deceased = $this->deceasedBuilder->addDeathCertificateId('888')->build();
        $this->assertInstanceOf(DeathCertificateId::class, $deceased->deathCertificateId());
        $this->assertSame('888', (string) $deceased->deathCertificateId());
    }

    public function testItAddsACauseOfDeath(): void
    {
        $deceased = $this->deceasedBuilder->addCauseOfDeath('Некоторая причина смерти')->build();
        $this->assertInstanceOf(CauseOfDeath::class, $deceased->causeOfDeath());
        $this->assertSame('Некоторая причина смерти', (string) $deceased->causeOfDeath());
    }

    public function testItIgnoresNullValues(): void
    {
        $deceased = $this->deceasedBuilder
            ->addDeathCertificateId(null)
            ->addCauseOfDeath(null)
            ->build();
        $this->assertNull($deceased->deathCertificateId());
        $this->assertNull($deceased->causeOfDeath());
    }
}
