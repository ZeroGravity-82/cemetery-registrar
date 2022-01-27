<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $naturalPersonId = new NaturalPersonId('777');
        $fullName        = new FullName('Ivanov Ivan Ivanovich');
        $bornAt          = new \DateTimeImmutable('2000-01-01');
        $naturalPerson   = new NaturalPerson(
            $naturalPersonId,
            $fullName,
            $bornAt,
        );

        $this->assertInstanceOf(NaturalPersonId::class, $naturalPerson->getId());
        $this->assertSame('777', (string) $naturalPerson->getId());
        $this->assertInstanceOf(FullName::class, $naturalPerson->getFullName());
        $this->assertSame('Ivanov Ivan Ivanovich', (string) $naturalPerson->getFullName());
        $this->assertInstanceOf(\DateTimeImmutable::class, $naturalPerson->getBornAt());
        $this->assertSame('2000-01-01', $naturalPerson->getBornAt()->format('Y-m-d'));
    }

    public function testItSuccessfullyCreatedWithoutOptionalFields(): void
    {
        $naturalPersonId = new NaturalPersonId('777');
        $naturalPerson   = new NaturalPerson($naturalPersonId, null, null);

        $this->assertInstanceOf(NaturalPersonId::class, $naturalPerson->getId());
        $this->assertSame('777', (string) $naturalPerson->getId());
        $this->assertNull($naturalPerson->getFullName());
        $this->assertNull($naturalPerson->getBornAt());
    }
}
