<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonBirthDetailsClarified;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\PlaceOfBirth;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonBirthDetailsClarifiedTest extends EventTest
{
    private NaturalPersonId    $id;
    private \DateTimeImmutable $bornAt;
    private PlaceOfBirth       $placeOfBirth;

    public function setUp(): void
    {
        $this->id           = new NaturalPersonId('NP001');
        $this->bornAt       = new \DateTimeImmutable('1990-04-13');
        $this->placeOfBirth = new PlaceOfBirth('Новосибирск');
        $this->event        = new NaturalPersonBirthDetailsClarified(
            $this->id,
            $this->bornAt,
            $this->placeOfBirth,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->bornAt === $this->event->bornAt());
        $this->assertTrue($this->placeOfBirth->isEqual($this->event->placeOfBirth()));
    }

    public function testItSuccessfullyCreatedWithoutBornAt(): void
    {
        $this->event = new NaturalPersonBirthDetailsClarified(
            $this->id,
            null,
            $this->placeOfBirth,
        );

        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertNull($this->event->bornAt());
        $this->assertTrue($this->placeOfBirth->isEqual($this->event->placeOfBirth()));
    }

    public function testItSuccessfullyCreatedWithoutPlaceOfBirth(): void
    {
        $this->event = new NaturalPersonBirthDetailsClarified(
            $this->id,
            $this->bornAt,
            null,
        );

        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->bornAt === $this->event->bornAt());
        $this->assertNull($this->event->placeOfBirth());
    }
}
