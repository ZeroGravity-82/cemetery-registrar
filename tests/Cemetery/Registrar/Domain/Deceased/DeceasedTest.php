<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\Deceased\CauseOfDeath;
use Cemetery\Registrar\Domain\Deceased\DeathCertificateId;
use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $id                 = new NaturalPersonId('NP001');
        $diedAt             = new \DateTimeImmutable('2022-01-10');
        $deathCertificateId = new DeathCertificateId('DC001');
        $causeOfDeath       = new CauseOfDeath('Some cause of death');
        $deceased           = new Deceased(
            $id,
            $diedAt,
            $deathCertificateId,
            $causeOfDeath,
        );

        $this->assertInstanceOf(NaturalPersonId::class, $deceased->getId());
        $this->assertSame('NP001', (string) $deceased->getId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $deceased->getDiedAt());
        $this->assertSame('2022-01-10', $deceased->getDiedAt()->format('Y-m-d'));
        $this->assertInstanceOf(DeathCertificateId::class, $deceased->getDeathCertificateId());
        $this->assertSame('DC001', (string) $deceased->getDeathCertificateId());
        $this->assertInstanceOf(CauseOfDeath::class, $deceased->getCauseOfDeath());
        $this->assertSame('Some cause of death', (string) $deceased->getCauseOfDeath());
        $this->assertInstanceOf(\DateTimeImmutable::class, $deceased->getCreatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $deceased->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $deceased->getUpdatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $deceased->getUpdatedAt());
    }

    public function testItSuccessfullyCreatedWithoutOptionalFields(): void
    {
        $id       = new NaturalPersonId('NP001');
        $diedAt   = new \DateTimeImmutable('2022-01-10');
        $deceased = new Deceased($id, $diedAt, null, null);

        $this->assertInstanceOf(NaturalPersonId::class, $deceased->getId());
        $this->assertSame('NP001', (string) $deceased->getId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $deceased->getDiedAt());
        $this->assertSame('2022-01-10', $deceased->getDiedAt()->format('Y-m-d'));
        $this->assertNull($deceased->getDeathCertificateId());
        $this->assertNull($deceased->getCauseOfDeath());
        $this->assertInstanceOf(\DateTimeImmutable::class, $deceased->getCreatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $deceased->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $deceased->getUpdatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $deceased->getUpdatedAt());
    }
}
