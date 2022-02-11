<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\IdentityGeneratorInterface;
use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonFactory;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonFactoryTest extends TestCase
{
    private NaturalPersonFactory $naturalPersonFactory;

    public function setUp(): void
    {
        $mockIdentityGenerator = $this->createMock(IdentityGeneratorInterface::class);
        $mockIdentityGenerator->method('getNextIdentity')->willReturn('777');
        $this->naturalPersonFactory = new NaturalPersonFactory($mockIdentityGenerator);
    }

    public function testItCreatesANaturalPerson(): void
    {
        $naturalPerson = $this->naturalPersonFactory->create(
            new FullName('Ivanov Ivan Ivanovich'),
            new \DateTimeImmutable('2000-01-01'),
        );
        $this->assertInstanceOf(NaturalPerson::class, $naturalPerson);
        $this->assertInstanceOf(FullName::class, $naturalPerson->getFullName());
        $this->assertSame('Ivanov Ivan Ivanovich', (string) $naturalPerson->getFullName());
        $this->assertNull($naturalPerson->getBornAt());
    }
}
