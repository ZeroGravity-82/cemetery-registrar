<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\AggregateRoot;
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
final class SoleProprietor extends AggregateRoot
{
    public const CLASS_SHORTCUT = 'SOLE_PROPRIETOR';
    public const CLASS_LABEL    = 'ИП';

    /**
     * @var Inn|null
     */
    private ?Inn $inn = null;

    /**
     * @var Ogrnip|null
     */
    private ?Ogrnip $ogrnip = null;

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
        private readonly SoleProprietorId $id,
        private Name                      $name,
    ) {
        parent::__construct();
    }

    /**
     * @return SoleProprietorId
     */
    public function id(): SoleProprietorId
    {
        return $this->id;
    }

    /**
     * @return Name
     */
    public function name(): Name
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
    public function inn(): ?Inn
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
    public function ogrnip(): ?Ogrnip
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
     * @return Okpo|null
     */
    public function okpo(): ?Okpo
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
    public function okved(): ?Okved
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
    public function registrationAddress(): ?Address
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
    public function actualLocationAddress(): ?Address
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
    public function bankDetails(): ?BankDetails
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
    public function phone(): ?PhoneNumber
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
    public function phoneAdditional(): ?PhoneNumber
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
    public function fax(): ?PhoneNumber
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
    public function email(): ?Email
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
    public function website(): ?Website
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
