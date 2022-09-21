<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonPassportClarified;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\Passport;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonPassportClarifiedTest extends EventTest
{
    private NaturalPersonId    $id;
    private Passport           $passport;

    public function setUp(): void
    {
        $this->id       = new NaturalPersonId('NP001');
        $this->passport = new Passport(
            '1234',
            '567890',
            new \DateTimeImmutable('2002-10-28'),
            'УВД Кировского района города Новосибирска',
            '540-001',
        );
        $this->event = new NaturalPersonPassportClarified(
            $this->id,
            $this->passport,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->passport->isEqual($this->event->passport()));
    }
}
