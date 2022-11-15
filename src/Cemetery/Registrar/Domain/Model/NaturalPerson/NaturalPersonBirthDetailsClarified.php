<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\AbstractEvent;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonBirthDetailsClarified extends AbstractEvent
{
    public function __construct(
        private NaturalPersonId     $id,
        private ?\DateTimeImmutable $bornAt,
        private ?PlaceOfBirth       $placeOfBirth,
    ) {
        parent::__construct();
    }

    public function id(): NaturalPersonId
    {
        return $this->id;
    }

    public function bornAt(): ?\DateTimeImmutable
    {
        return $this->bornAt;
    }

    public function placeOfBirth(): ?PlaceOfBirth
    {
        return $this->placeOfBirth;
    }
}
