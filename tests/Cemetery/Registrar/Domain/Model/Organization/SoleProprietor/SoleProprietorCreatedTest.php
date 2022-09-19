<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Organization\Name;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorCreated;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorCreatedTest extends EventTest
{
    private SoleProprietorId $id;
    private Name             $name;
    private ?Inn             $inn;

    public function setUp(): void
    {
        $this->id                = new SoleProprietorId('888');
        $this->name              = new Name('ИП Иванов Иван Иванович');
        $this->inn = new Inn('772208786091');
        $this->event             = new SoleProprietorCreated(
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
        $this->event = new SoleProprietorCreated(
            $this->id,
            $this->name,
            null,
        );
        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->name->isEqual($this->event->name()));
        $this->assertNull($this->event->inn());
    }
}
