<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Event;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonPassportClarified extends Event
{
    public function __construct(
        private NaturalPersonId $id,
        private Passport        $passport,
    ) {
        parent::__construct();
    }

    public function id(): NaturalPersonId
    {
        return $this->id;
    }

    public function passport(): Passport
    {
        return $this->passport;
    }
}
