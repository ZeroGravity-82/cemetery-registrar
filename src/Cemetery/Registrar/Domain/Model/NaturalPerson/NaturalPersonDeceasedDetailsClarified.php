<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Event;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetails;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonDeceasedDetailsClarified extends Event
{
    public function __construct(
        private NaturalPersonId $id,
        private DeceasedDetails $deceasedDetails,
    ) {
        parent::__construct();
    }

    public function id(): NaturalPersonId
    {
        return $this->id;
    }

    public function deceasedDetails(): DeceasedDetails
    {
        return $this->deceasedDetails;
    }
}
