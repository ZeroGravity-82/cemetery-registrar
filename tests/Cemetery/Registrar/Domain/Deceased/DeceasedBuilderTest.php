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
        $this->assertInstanceOf(DeceasedId::class, $deceased->getId());
        $this->assertSame('555', (string) $deceased->getId());
        $this->assertInstanceOf(NaturalPersonId::class, $deceased->getNaturalPersonId());
        $this->assertSame('777', (string) $deceased->getNaturalPersonId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $deceased->getDiedAt());
        $this->assertSame('2021-02-12', $deceased->getDiedAt()->format('Y-m-d'));
        $this->assertNull($deceased->getDeathCertificateId());
        $this->assertNull($deceased->getCauseOfDeath());
    }

    public function testItFailsToBuildADeceasedBeforeInitialization(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Строитель для класса Deceased не инициализирован.');

        $deceasedBuilder = new DeceasedBuilder($this->mockIdentityGenerator);
        $deceasedBuilder->build();
    }

    public function testItAddsADeathCertificateId(): void
    {
        $deceased = $this->deceasedBuilder->addDeathCertificateId('888')->build();
        $this->assertInstanceOf(DeathCertificateId::class, $deceased->getDeathCertificateId());
        $this->assertSame('888', (string) $deceased->getDeathCertificateId());
    }

    public function testItAddsACauseOfDeath(): void
    {
        $deceased = $this->deceasedBuilder->addCauseOfDeath('Некоторая причина смерти')->build();
        $this->assertInstanceOf(CauseOfDeath::class, $deceased->getCauseOfDeath());
        $this->assertSame('Некоторая причина смерти', (string) $deceased->getCauseOfDeath());
    }

    public function testItIgnoresNullValues(): void
    {
        $deceased = $this->deceasedBuilder
            ->addDeathCertificateId(null)
            ->addCauseOfDeath(null)
            ->build();
        $this->assertNull($deceased->getDeathCertificateId());
        $this->assertNull($deceased->getCauseOfDeath());
    }
}
