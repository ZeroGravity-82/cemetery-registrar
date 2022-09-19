<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Event;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonCreated extends Event
{
    public function __construct(
        private NaturalPersonId     $id,
        private FullName            $fullName,
        private ?\DateTimeImmutable $bornAt,
        private ?\DateTimeImmutable $diedAt,
    ) {
        parent::__construct();
    }

    public function id(): NaturalPersonId
    {
        return $this->id;
    }

    public function fullName(): FullName
    {
        return $this->fullName;
    }

    public function bornAt(): ?\DateTimeImmutable
    {
        return $this->bornAt;
    }

    public function diedAt(): ?\DateTimeImmutable
    {
        return $this->diedAt;
    }
}
