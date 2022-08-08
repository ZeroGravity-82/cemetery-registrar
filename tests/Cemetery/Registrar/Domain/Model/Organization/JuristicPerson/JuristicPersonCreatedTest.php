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
    private JuristicPersonId $juristicPersonId;
    private Name             $juristicPersonName;
    private Inn              $juristicPersonInn;

    public function setUp(): void
    {
        $this->juristicPersonId   = new JuristicPersonId('888');
        $this->juristicPersonName = new Name('ООО "Рога и копыта"');
        $this->juristicPersonInn  = new Inn('7728168971');
        $this->event              = new JuristicPersonCreated(
            $this->juristicPersonId,
            $this->juristicPersonName,
            $this->juristicPersonInn,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->juristicPersonId->isEqual($this->event->juristicPersonId()));
        $this->assertTrue($this->juristicPersonName->isEqual($this->event->juristicPersonName()));
        $this->assertTrue($this->juristicPersonInn->isEqual($this->event->juristicPersonInn()));
    }
}
