<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\Inn;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonCreated;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\Name;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonCreatedTest extends EventTest
{
    private JuristicPersonId $id;
    private Name             $name;
    private ?Inn             $inn;

    public function setUp(): void
    {
        $this->id                = new JuristicPersonId('888');
        $this->name              = new Name('ООО "Рога и копыта"');
        $this->inn = new Inn('7728168971');
        $this->event             = new JuristicPersonCreated(
            $this->id,
            $this->name,
            $this->inn,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->name->isEqual($this->event->name()));
        $this->assertTrue($this->inn->isEqual($this->event->inn()));
    }

    public function testItSuccessfullyCreatedWithoutInn(): void
    {
        $this->event = new JuristicPersonCreated(
            $this->id,
            $this->name,
            null,
        );
        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->name->isEqual($this->event->name()));
        $this->assertNull($this->event->inn());
    }
}
