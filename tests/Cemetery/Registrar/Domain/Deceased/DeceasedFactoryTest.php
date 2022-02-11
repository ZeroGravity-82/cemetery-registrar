<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\Deceased\DeceasedFactory;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\IdentityGeneratorInterface;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedFactoryTest extends TestCase
{
    private DeceasedFactory $deceasedFactory;

    public function setUp(): void
    {
        $mockIdentityGenerator = $this->createMock(IdentityGeneratorInterface::class);
        $mockIdentityGenerator->method('getNextIdentity')->willReturn('D001');
        $this->deceasedFactory = new DeceasedFactory($mockIdentityGenerator);
    }

    public function testItCreatesADeceased(): void
    {
        $deceased = $this->deceasedFactory->create(
            new NaturalPersonId('NP001'),
            new \DateTimeImmutable('2022-02-09'),
        );

        $this->assertInstanceOf(DeceasedId::class, $deceased->getId());
        $this->assertSame('D001', (string) $deceased->getId());
        $this->assertInstanceOf(NaturalPersonId::class, $deceased->getNaturalPersonId());
        $this->assertSame('NP001', (string) $deceased->getNaturalPersonId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $deceased->getDiedAt());
        $this->assertSame('2022-02-09', $deceased->getDiedAt()->format('Y-m-d'));
        $this->assertNull($deceased->getDeathCertificateId());
        $this->assertNull($deceased->getCauseOfDeath());
    }
}
