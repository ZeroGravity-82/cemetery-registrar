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
    private NaturalPersonId    $id;
    private FullName           $fullName;
    private \DateTimeImmutable $bornAt;
    private \DateTimeImmutable $diedAt;

    public function setUp(): void
    {
        $this->id       = new NaturalPersonId('NP001');
        $this->fullName = new FullName('Иванов Иван Иванович');
        $this->bornAt   = new \DateTimeImmutable('1924-02-15');
        $this->diedAt   = new \DateTimeImmutable('2003-07-30');
        $this->event    = new NaturalPersonCreated(
            $this->id,
            $this->fullName,
            $this->bornAt,
            $this->diedAt,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->fullName->isEqual($this->event->fullName()));
        $this->assertTrue($this->bornAt === $this->event->bornAt());
        $this->assertTrue($this->diedAt === $this->event->diedAt());
    }

    public function testItSuccessfullyCreatedWithoutBornAt(): void
    {
        $this->event = new NaturalPersonCreated(
            $this->id,
            $this->fullName,
            null,
            $this->diedAt,
        );
        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->fullName->isEqual($this->event->fullName()));
        $this->assertNull($this->event->bornAt());
        $this->assertTrue($this->diedAt === $this->event->diedAt());
    }

    public function testItSuccessfullyCreatedWithoutDiedAt(): void
    {
        $this->event = new NaturalPersonCreated(
            $this->id,
            $this->fullName,
            $this->bornAt,
            null,
        );
        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->fullName->isEqual($this->event->fullName()));
        $this->assertTrue($this->bornAt === $this->event->bornAt());
        $this->assertNull($this->event->diedAt());
    }
}
