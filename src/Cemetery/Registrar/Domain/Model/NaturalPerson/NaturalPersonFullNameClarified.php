<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Event;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonFullNameClarified extends Event
{
    public function __construct(
        private NaturalPersonId $id,
        private FullName        $fullName,
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
}
