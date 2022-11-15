<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\AbstractEvent;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonContactClarified extends AbstractEvent
{
    public function __construct(
        private NaturalPersonId $id,
        private ?PhoneNumber    $phone,
        private ?PhoneNumber    $phoneAdditional,
        private ?Address        $address,
        private ?Email          $email,
    ) {
        parent::__construct();
    }

    public function id(): NaturalPersonId
    {
        return $this->id;
    }

    public function phone(): ?PhoneNumber
    {
        return $this->phone;
    }

    public function phoneAdditional(): ?PhoneNumber
    {
        return $this->phoneAdditional;
    }

    public function address(): ?Address
    {
        return $this->address;
    }

    public function email(): ?Email
    {
        return $this->email;
    }
}
