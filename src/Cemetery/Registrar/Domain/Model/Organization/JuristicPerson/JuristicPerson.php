<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\Contact\Website;
use Cemetery\Registrar\Domain\Model\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\Model\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Model\Organization\Name;
use Cemetery\Registrar\Domain\Model\Organization\Okved;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPerson extends AbstractAggregateRoot
{
    public const CLASS_SHORTCUT = 'JURISTIC_PERSON';
    public const CLASS_LABEL    = 'юрлицо';

    private ?Inn         $inn = null;
    private ?Kpp         $kpp = null;
    private ?Ogrn        $ogrn = null;
    private ?Okpo        $okpo = null;
    private ?Okved       $okved = null;
    private ?Address     $legalAddress = null;
    private ?Address     $postalAddress = null;
    private ?BankDetails $bankDetails = null;
    private ?PhoneNumber $phone = null;
    private ?PhoneNumber $phoneAdditional = null;
    private ?PhoneNumber $fax = null;
    private ?FullName    $generalDirector = null;
    private ?Email       $email = null;
    private ?Website     $website = null;

    public function __construct(
        private JuristicPersonId $id,
        private Name             $name,
    ) {
        parent::__construct();
    }

    public function id(): JuristicPersonId
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

    public function kpp(): ?Kpp
    {
        return $this->kpp;
    }

    public function setKpp(?Kpp $kpp): self
    {
        $this->kpp = $kpp;

        return $this;
    }

    public function ogrn(): ?Ogrn
    {
        return $this->ogrn;
    }

    public function setOgrn(?Ogrn $ogrn): self
    {
        $this->ogrn = $ogrn;

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

    public function legalAddress(): ?Address
    {
        return $this->legalAddress;
    }

    public function setLegalAddress(?Address $legalAddress): self
    {
        $this->legalAddress = $legalAddress;

        return $this;
    }

    public function postalAddress(): ?Address
    {
        return $this->postalAddress;
    }

    public function setPostalAddress(?Address $postalAddress): self
    {
        $this->postalAddress = $postalAddress;

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

    public function generalDirector(): ?FullName
    {
        return $this->generalDirector;
    }

    public function setGeneralDirector(?FullName $generalDirector): self
    {
        $this->generalDirector = $generalDirector;

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
