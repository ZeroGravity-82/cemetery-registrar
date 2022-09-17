<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonCreated;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonCreatedTest extends EventTest
{
    private NaturalPersonId    $naturalPersonId;
    private FullName           $naturalPersonFullName;
    private \DateTimeImmutable $naturalPersonBornAt;
    private \DateTimeImmutable $naturalPersonDiedAt;

    public function setUp(): void
    {
        $this->naturalPersonId       = new NaturalPersonId('NP001');
        $this->naturalPersonFullName = new FullName('Иванов Иван Иванович');
        $this->naturalPersonBornAt   = new \DateTimeImmutable('1924-02-15');
        $this->naturalPersonDiedAt   = new \DateTimeImmutable('2003-07-30');
        $this->event                 = new NaturalPersonCreated(
            $this->naturalPersonId,
            $this->naturalPersonFullName,
            $this->naturalPersonBornAt,
            $this->naturalPersonDiedAt,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->naturalPersonId->isEqual($this->event->naturalPersonId()));
        $this->assertTrue($this->naturalPersonFullName->isEqual($this->event->naturalPersonFullName()));
        $this->assertTrue($this->naturalPersonBornAt === $this->event->naturalPersonBornAt());
        $this->assertTrue($this->naturalPersonDiedAt === $this->event->naturalPersonDiedAt());
    }

    public function testItSuccessfullyCreatedWithoutBornAt(): void
    {
        $this->event = new NaturalPersonCreated(
            $this->naturalPersonId,
            $this->naturalPersonFullName,
            null,
            $this->naturalPersonDiedAt,
        );
        $this->assertTrue($this->naturalPersonId->isEqual($this->event->naturalPersonId()));
        $this->assertTrue($this->naturalPersonFullName->isEqual($this->event->naturalPersonFullName()));
        $this->assertNull($this->event->naturalPersonBornAt());
        $this->assertTrue($this->naturalPersonDiedAt === $this->event->naturalPersonDiedAt());
    }

    public function testItSuccessfullyCreatedWithoutDiedAt(): void
    {
        $this->event = new NaturalPersonCreated(
            $this->naturalPersonId,
            $this->naturalPersonFullName,
            $this->naturalPersonBornAt,
            null,
        );
        $this->assertTrue($this->naturalPersonId->isEqual($this->event->naturalPersonId()));
        $this->assertTrue($this->naturalPersonFullName->isEqual($this->event->naturalPersonFullName()));
        $this->assertTrue($this->naturalPersonBornAt === $this->event->naturalPersonBornAt());
        $this->assertNull($this->event->naturalPersonDiedAt());
    }
}
