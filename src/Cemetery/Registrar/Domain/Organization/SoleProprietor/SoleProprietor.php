<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\Okved;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class SoleProprietor extends AbstractAggregateRoot
{
    /**
     * @var Inn|null
     */
    private ?Inn $inn = null;

    /**
     * @var Ogrnip|null
     */
    private ?Ogrnip $ogrnip = null;

    /**
     * @var Okved|null
     */
    private ?Okved $okved = null;

    /**
     * @var Address|null
     */
    private ?Address $registrationAddress = null;

    /**
     * @var Address|null
     */
    private ?Address $actualLocationAddress = null;

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
     * @var Email|null
     */
    private ?Email $email = null;

    /**
     * @var Website|null
     */
    private ?Website $website = null;

    /**
     * @param SoleProprietorId $id
     * @param Name             $name
     */
    public function __construct(
        private SoleProprietorId $id,
        private Name             $name,
    ) {
        parent::__construct();
    }

    /**
     * @return SoleProprietorId
     */
    public function getId(): SoleProprietorId
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
     * @return Ogrnip|null
     */
    public function getOgrnip(): ?Ogrnip
    {
        return $this->ogrnip;
    }

    /**
     * @param Ogrnip|null $ogrnip
     *
     * @return $this
     */
    public function setOgrnip(?Ogrnip $ogrnip): self
    {
        $this->ogrnip = $ogrnip;

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
    public function getRegistrationAddress(): ?Address
    {
        return $this->registrationAddress;
    }

    /**
     * @param Address|null $registrationAddress
     *
     * @return $this
     */
    public function setRegistrationAddress(?Address $registrationAddress): self
    {
        $this->registrationAddress = $registrationAddress;

        return $this;
    }

    /**
     * @return Address|null
     */
    public function getActualLocationAddress(): ?Address
    {
        return $this->actualLocationAddress;
    }

    /**
     * @param Address|null $actualLocationAddress
     *
     * @return $this
     */
    public function setActualLocationAddress(?Address $actualLocationAddress): self
    {
        $this->actualLocationAddress = $actualLocationAddress;

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
