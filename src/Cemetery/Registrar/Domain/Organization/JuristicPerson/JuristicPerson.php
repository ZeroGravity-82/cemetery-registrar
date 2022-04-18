<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\Okved;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class JuristicPerson extends AbstractAggregateRoot
{
    /**
     * @var Inn|null
     */
    private ?Inn $inn = null;

    /**
     * @var Kpp|null
     */
    private ?Kpp $kpp = null;

    /**
     * @var Ogrn|null
     */
    private ?Ogrn $ogrn = null;

    /**
     * @var Okpo|null
     */
    private ?Okpo $okpo = null;

    /**
     * @var Okved|null
     */
    private ?Okved $okved = null;

    /**
     * @var Address|null
     */
    private ?Address $legalAddress = null;

    /**
     * @var Address|null
     */
    private ?Address $postalAddress = null;

    /**
     * @var BankDetails|null
     */
    private ?BankDetails $bankDetails = null;

    /**
     * @var PhoneNumber|null
     */
    private ?PhoneNumber $phone = null;

    /**
     * @var PhoneNumber|null
     */
    private ?PhoneNumber $phoneAdditional = null;

    /**
     * @var PhoneNumber|null
     */
    private ?PhoneNumber $fax = null;

    /**
     * @var FullName|null
     */
    private ?FullName $generalDirector = null;

    /**
     * @var Email|null
     */
    private ?Email $email = null;

    /**
     * @var Website|null
     */
    private ?Website $website = null;

    /**
     * @param JuristicPersonId $id
     * @param Name             $name
     */
    public function __construct(
        private readonly JuristicPersonId $id,
        private Name                      $name,
    ) {
        parent::__construct();
    }

    /**
     * @return JuristicPersonId
     */
    public function getId(): JuristicPersonId
    {
        return $this->id;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @param Name $name
     *
     * @return $this
     */
    public function setName(Name $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Inn|null
     */
    public function getInn(): ?Inn
    {
        return $this->inn;
    }

    /**
     * @param Inn|null $inn
     *
     * @return $this
     */
    public function setInn(?Inn $inn): self
    {
        $this->inn = $inn;

        return $this;
    }

    /**
     * @return Kpp|null
     */
    public function getKpp(): ?Kpp
    {
        return $this->kpp;
    }

    /**
     * @param Kpp|null $kpp
     *
     * @return $this
     */
    public function setKpp(?Kpp $kpp): self
    {
        $this->kpp = $kpp;

        return $this;
    }

    /**
     * @return Ogrn|null
     */
    public function getOgrn(): ?Ogrn
    {
        return $this->ogrn;
    }

    /**
     * @param Ogrn|null $ogrn
     *
     * @return $this
     */
    public function setOgrn(?Ogrn $ogrn): self
    {
        $this->ogrn = $ogrn;

        return $this;
    }

    /**
     * @return Okpo|null
     */
    public function getOkpo(): ?Okpo
    {
        return $this->okpo;
    }

    /**
     * @param Okpo|null $okpo
     *
     * @return $this
     */
    public function setOkpo(?Okpo $okpo): self
    {
        $this->okpo = $okpo;

        return $this;
    }

    /**
     * @return Okved|null
     */
    public function getOkved(): ?Okved
    {
        return $this->okved;
    }

    /**
     * @param Okved|null $okved
     *
     * @return $this
     */
    public function setOkved(?Okved $okved): self
    {
        $this->okved = $okved;

        return $this;
    }

    /**
     * @return Address|null
     */
    public function getLegalAddress(): ?Address
    {
        return $this->legalAddress;
    }

    /**
     * @param Address|null $legalAddress
     *
     * @return $this
     */
    public function setLegalAddress(?Address $legalAddress): self
    {
        $this->legalAddress = $legalAddress;

        return $this;
    }

    /**
     * @return Address|null
     */
    public function getPostalAddress(): ?Address
    {
        return $this->postalAddress;
    }

    /**
     * @param Address|null $postalAddress
     *
     * @return $this
     */
    public function setPostalAddress(?Address $postalAddress): self
    {
        $this->postalAddress = $postalAddress;

        return $this;
    }

    /**
     * @return BankDetails|null
     */
    public function getBankDetails(): ?BankDetails
    {
        return $this->bankDetails;
    }

    /**
     * @param BankDetails|null $bankDetails
     *
     * @return $this
     */
    public function setBankDetails(?BankDetails $bankDetails): self
    {
        $this->bankDetails = $bankDetails;

        return $this;
    }

    /**
     * @return PhoneNumber|null
     */
    public function getPhone(): ?PhoneNumber
    {
        return $this->phone;
    }

    /**
     * @param PhoneNumber|null $phone
     *
     * @return $this
     */
    public function setPhone(?PhoneNumber $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return PhoneNumber|null
     */
    public function getPhoneAdditional(): ?PhoneNumber
    {
        return $this->phoneAdditional;
    }

    /**
     * @param PhoneNumber|null $phoneAdditional
     *
     * @return $this
     */
    public function setPhoneAdditional(?PhoneNumber $phoneAdditional): self
    {
        $this->phoneAdditional = $phoneAdditional;

        return $this;
    }

    /**
     * @return PhoneNumber|null
     */
    public function getFax(): ?PhoneNumber
    {
        return $this->fax;
    }

    /**
     * @param PhoneNumber|null $fax
     *
     * @return $this
     */
    public function setFax(?PhoneNumber $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * @return FullName|null
     */
    public function getGeneralDirector(): ?FullName
    {
        return $this->generalDirector;
    }

    /**
     * @param FullName|null $generalDirector
     *
     * @return $this
     */
    public function setGeneralDirector(?FullName $generalDirector): self
    {
        $this->generalDirector = $generalDirector;

        return $this;
    }

    /**
     * @return Email|null
     */
    public function getEmail(): ?Email
    {
        return $this->email;
    }

    /**
     * @param Email|null $email
     *
     * @return $this
     */
    public function setEmail(?Email $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Website|null
     */
    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    /**
     * @param Website|null $website
     *
     * @return $this
     */
    public function setWebsite(?Website $website): self
    {
        $this->website = $website;

        return $this;
    }
}
