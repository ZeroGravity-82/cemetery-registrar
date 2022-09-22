<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\Age;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\CremationCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeathCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetails;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonDeceasedDetailsClarified;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonDeceasedDetailsClarifiedTest extends EventTest
{
    private NaturalPersonId $id;
    private DeceasedDetails $deceasedDetails;

    public function setUp(): void
    {
        $this->id              = new NaturalPersonId('NP001');
        $this->deceasedDetails = new DeceasedDetails(
            new \DateTimeImmutable('2021-12-01'),
            new Age(69),
            new CauseOfDeathId('CD008'),
            new DeathCertificate('V-МЮ', '532515', new \DateTimeImmutable('2021-12-02')),
            new CremationCertificate('12964', new \DateTimeImmutable('2021-12-05')),
        );
        $this->event = new NaturalPersonDeceasedDetailsClarified(
            $this->id,
            $this->deceasedDetails,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->deceasedDetails->isEqual($this->event->deceasedDetails()));
    }
}
