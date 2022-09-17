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
        private NaturalPersonId     $naturalPersonId,
        private FullName            $naturalPersonFullName,
        private ?\DateTimeImmutable $naturalPersonBornAt,
        private ?\DateTimeImmutable $naturalPersonDiedAt,
    ) {
        parent::__construct();
    }

    public function naturalPersonId(): NaturalPersonId
    {
        return $this->naturalPersonId;
    }

    public function naturalPersonFullName(): FullName
    {
        return $this->naturalPersonFullName;
    }

    public function naturalPersonBornAt(): ?\DateTimeImmutable
    {
        return $this->naturalPersonBornAt;
    }

    public function naturalPersonDiedAt(): ?\DateTimeImmutable
    {
        return $this->naturalPersonDiedAt;
    }
}
