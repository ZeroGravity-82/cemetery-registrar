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
    private NaturalPerson $naturalPerson;
    
    public function setUp(): void
    {
        $naturalPersonId     = new NaturalPersonId('777');
        $fullName            = new FullName('Ivanov Ivan Ivanovich');
        $this->naturalPerson = new NaturalPerson($naturalPersonId, $fullName);
    }
    
    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(NaturalPersonId::class, $this->naturalPerson->getId());
        $this->assertSame('777', (string) $this->naturalPerson->getId());
        $this->assertInstanceOf(FullName::class, $this->naturalPerson->getFullName());
        $this->assertSame('Ivanov Ivan Ivanovich', (string) $this->naturalPerson->getFullName());
        $this->assertNull($this->naturalPerson->getBornAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->naturalPerson->getCreatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->naturalPerson->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->naturalPerson->getUpdatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->naturalPerson->getUpdatedAt());
    }
    
    public function testItSetsBornAt(): void
    {
        $bornAt = new \DateTimeImmutable('2000-01-01');
        $this->naturalPerson->setBornAt($bornAt);
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->naturalPerson->getBornAt());
        $this->assertSame('2000-01-01', $this->naturalPerson->getBornAt()->format('Y-m-d'));
    }


}
