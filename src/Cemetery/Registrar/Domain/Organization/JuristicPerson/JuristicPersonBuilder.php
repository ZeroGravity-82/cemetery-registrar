<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Domain\IdentityGeneratorInterface;
use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\Okved;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class JuristicPersonBuilder
{
    /**
     * @var JuristicPerson
     */
    private JuristicPerson $juristicPerson;

    /**
     * @param IdentityGeneratorInterface $identityGenerator
     */
    public function __construct(
        private readonly IdentityGeneratorInterface $identityGenerator,
    ) {}

    /**
     * @param string $name
     *
     * @return $this
     */
    public function initialize(string $name): self
    {
        $name                 = new Name($name);
        $this->juristicPerson = new JuristicPerson(
            new JuristicPersonId($this->identityGenerator->getNextIdentity()),
            $name,
        );

        return $this;
    }

    /**
     * @param string $inn
     *
     * @return $this
     */
    public function addInn(string $inn): self
    {
        $this->assertInitialized();
        $inn = new Inn($inn);
        $this->juristicPerson->setInn($inn);

        return $this;
    }

    /**
     * @param string $kpp
     *
     * @return $this
     */
    public function addKpp(string $kpp): self
    {
        $this->assertInitialized();
        $kpp = new Kpp($kpp);
        $this->juristicPerson->setKpp($kpp);

        return $this;
    }

    /**
     * @param string $ogrn
     *
     * @return $this
     */
    public function addOgrn(string $ogrn): self
    {
        $this->assertInitialized();
        $ogrn = new Ogrn($ogrn);
        $this->juristicPerson->setOgrn($ogrn);

        return $this;
    }

    /**
     * @param string $okpo
     *
     * @return $this
     */
    public function addOkpo(string $okpo): self
    {
        $this->assertInitialized();
        $okpo = new Okpo($okpo);
        $this->juristicPerson->setOkpo($okpo);

        return $this;
    }

    /**
     * @param string $okved
     *
     * @return $this
     */
    public function addOkved(string $okved): self
    {
        $this->assertInitialized();
        $okved = new Okved($okved);
        $this->juristicPerson->setOkved($okved);

        return $this;
    }


    /**
     * @param string $legalAddress
     *
     * @return $this
     */
    public function addLegalAddress(string $legalAddress): self
    {
        $this->assertInitialized();
        $legalAddress = new Address($legalAddress);
        $this->juristicPerson->setLegalAddress($legalAddress);

        return $this;
    }

    /**
     * @param string $postalAddress
     *
     * @return $this
     */
    public function addPostalAddress(string $postalAddress): self
    {
        $this->assertInitialized();
        $postalAddress = new Address($postalAddress);
        $this->juristicPerson->setPostalAddress($postalAddress);

        return $this;
    }

    /**
     * @param string      $bankName
     * @param string      $bik
     * @param string|null $correspondentAccount
     * @param string      $currentAccount
     *
     * @return $this
     */
    public function addBankDetails(
        string  $bankName,
        string  $bik,
        ?string $correspondentAccount,
        string  $currentAccount,
    ): self {
        $this->assertInitialized();
        $bankDetails = new BankDetails($bankName, $bik, $correspondentAccount, $currentAccount);
        $this->juristicPerson->setBankDetails($bankDetails);

        return $this;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function addPhone(string $phone): self
    {
        $this->assertInitialized();
        $phone = new PhoneNumber($phone);
        $this->juristicPerson->setPhone($phone);

        return $this;
    }

    /**
     * @param string $phoneAdditional
     *
     * @return $this
     */
    public function addPhoneAdditional(string $phoneAdditional): self
    {
        $this->assertInitialized();
        $phoneAdditional = new PhoneNumber($phoneAdditional);
        $this->juristicPerson->setPhoneAdditional($phoneAdditional);

        return $this;
    }

    /**
     * @param string $fax
     *
     * @return $this
     */
    public function addFax(string $fax): self
    {
        $this->assertInitialized();
        $fax = new PhoneNumber($fax);
        $this->juristicPerson->setFax($fax);

        return $this;
    }

    /**
     * @param string $generalDirector
     *
     * @return $this
     */
    public function addGeneralDirector(string $generalDirector): self
    {
        $this->assertInitialized();
        $generalDirector = new FullName($generalDirector);
        $this->juristicPerson->setGeneralDirector($generalDirector);

        return $this;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function addEmail(string $email): self
    {
        $this->assertInitialized();
        $email = new Email($email);
        $this->juristicPerson->setEmail($email);

        return $this;
    }

    /**
     * @param string $website
     *
     * @return $this
     */
    public function addWebsite(string $website): self
    {
        $this->assertInitialized();
        $website = new Website($website);
        $this->juristicPerson->setWebsite($website);

        return $this;
    }

    /**
     * @return JuristicPerson
     */
    public function build(): JuristicPerson
    {
        $this->assertInitialized();
        $juristicPerson = $this->juristicPerson;
        unset($this->juristicPerson);

        return $juristicPerson;
    }

    /**
     * @throws \LogicException when the juristic person builder is not initialized
     */
    private function assertInitialized(): void
    {
        if (!isset($this->juristicPerson)) {
            throw new \LogicException(\sprintf('Строитель для класса %s не инициализирован.', JuristicPerson::class));
        }
    }
}
