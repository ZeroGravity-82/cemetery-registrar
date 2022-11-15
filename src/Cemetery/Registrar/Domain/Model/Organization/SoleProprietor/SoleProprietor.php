<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\Contact\Website;
use Cemetery\Registrar\Domain\Model\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Model\Organization\Name;
use Cemetery\Registrar\Domain\Model\Organization\Okved;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietor extends AbstractAggregateRoot
{
    public const CLASS_SHORTCUT = 'SOLE_PROPRIETOR';
    public const CLASS_LABEL    = 'ИП';

    private ?Inn         $inn = null;
    private ?Ogrnip      $ogrnip = null;
    private ?Okpo        $okpo = null;
    private ?Okved       $okved = null;
    private ?Address     $registrationAddress = null;
    private ?Address     $actualLocationAddress = null;
    private ?BankDetails $bankDetails = null;
    private ?PhoneNumber $phone = null;
    private ?PhoneNumber $phoneAdditional = null;
    private ?PhoneNumber $fax = null;
    private ?Email       $email = null;
    private ?Website     $website = null;

    public function __construct(
        private SoleProprietorId $id,
        private Name             $name,
    ) {
        parent::__construct();
    }

    public function id(): SoleProprietorId
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function setName(Name $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function inn(): ?Inn
    {
        return $this->inn;
    }

    public function setInn(?Inn $inn): self
    {
        $this->inn = $inn;

        return $this;
    }

    public function ogrnip(): ?Ogrnip
    {
        return $this->ogrnip;
    }

    public function setOgrnip(?Ogrnip $ogrnip): self
    {
        $this->ogrnip = $ogrnip;

        return $this;
    }

    public function okpo(): ?Okpo
    {
        return $this->okpo;
    }

    public function setOkpo(?Okpo $okpo): self
    {
        $this->okpo = $okpo;

        return $this;
    }

    public function okved(): ?Okved
    {
        return $this->okved;
    }

    public function setOkved(?Okved $okved): self
    {
        $this->okved = $okved;

        return $this;
    }

    public function registrationAddress(): ?Address
    {
        return $this->registrationAddress;
    }

    public function setRegistrationAddress(?Address $registrationAddress): self
    {
        $this->registrationAddress = $registrationAddress;

        return $this;
    }

    public function actualLocationAddress(): ?Address
    {
        return $this->actualLocationAddress;
    }

    public function setActualLocationAddress(?Address $actualLocationAddress): self
    {
        $this->actualLocationAddress = $actualLocationAddress;

        return $this;
    }

    public function bankDetails(): ?BankDetails
    {
        return $this->bankDetails;
    }

    public function setBankDetails(?BankDetails $bankDetails): self
    {
        $this->bankDetails = $bankDetails;

        return $this;
    }

    public function phone(): ?PhoneNumber
    {
        return $this->phone;
    }

    public function setPhone(?PhoneNumber $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function phoneAdditional(): ?PhoneNumber
    {
        return $this->phoneAdditional;
    }

    public function setPhoneAdditional(?PhoneNumber $phoneAdditional): self
    {
        $this->phoneAdditional = $phoneAdditional;

        return $this;
    }

    public function fax(): ?PhoneNumber
    {
        return $this->fax;
    }

    public function setFax(?PhoneNumber $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function email(): ?Email
    {
        return $this->email;
    }

    public function setEmail(?Email $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function website(): ?Website
    {
        return $this->website;
    }

    public function setWebsite(?Website $website): self
    {
        $this->website = $website;

        return $this;
    }
}
