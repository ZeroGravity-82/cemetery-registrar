<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\Inn;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonCreated;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Tests\Registrar\Domain\AbstractEventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonCreatedTest extends AbstractEventTest
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
        $this->assertSame($this->juristicPersonId, $this->event->getJuristicPersonId());
        $this->assertSame($this->juristicPersonName, $this->event->getJuristicPersonName());
        $this->assertSame($this->juristicPersonInn, $this->event->getJuristicPersonInn());
    }
}