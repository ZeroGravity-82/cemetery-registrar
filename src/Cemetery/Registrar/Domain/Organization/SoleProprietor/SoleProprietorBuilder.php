<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Domain\IdentityGeneratorInterface;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\Name;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class SoleProprietorBuilder
{
    /**
     * @var SoleProprietor
     */
    private SoleProprietor $soleProprietor;

    /**
     * @param IdentityGeneratorInterface $identityGenerator
     */
    public function __construct(
        private IdentityGeneratorInterface $identityGenerator,
    ) {
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function initialize(string $name): self
    {
        $name                 = new Name($name);
        $this->soleProprietor = new SoleProprietor(
            new SoleProprietorId($this->identityGenerator->getNextIdentity()),
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
        $this->soleProprietor->setInn($inn);

        return $this;
    }

    /**
     * @param string $ogrnip
     *
     * @return $this
     */
    public function addOgrnip(string $ogrnip): self
    {
        $this->assertInitialized();
        $ogrnip = new Ogrnip($ogrnip);
        $this->soleProprietor->setOgrnip($ogrnip);

        return $this;
    }

    /**
     * @param string $registrationAddress
     *
     * @return $this
     */
    public function addRegistrationAddress(string $registrationAddress): self
    {
        $this->assertInitialized();
        $registrationAddress = new Address($registrationAddress);
        $this->soleProprietor->setRegistrationAddress($registrationAddress);

        return $this;
    }

    /**
     * @param string $actualLocationAddress
     *
     * @return $this
     */
    public function addActualLocationAddress(string $actualLocationAddress): self
    {
        $this->assertInitialized();
        $actualLocationAddress = new Address($actualLocationAddress);
        $this->soleProprietor->setActualLocationAddress($actualLocationAddress);

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
        $this->soleProprietor->setBankDetails($bankDetails);

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
        $this->soleProprietor->setPhone($phone);

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
        $this->soleProprietor->setPhoneAdditional($phoneAdditional);

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
        $this->soleProprietor->setFax($fax);

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
        $this->soleProprietor->setEmail($email);

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
        $this->soleProprietor->setWebsite($website);

        return $this;
    }

    /**
     * @return SoleProprietor
     */
    public function build(): SoleProprietor
    {
        $this->assertInitialized();
        $soleProprietor = $this->soleProprietor;
        unset($this->soleProprietor);

        return $soleProprietor;
    }

    /**
     * @throws \LogicException when the sole proprietor builder is not initialized
     */
    private function assertInitialized(): void
    {
        if (!isset($this->soleProprietor)) {
            throw new \LogicException('Строитель для класса SoleProprietor не инициализирован.');
        }
    }
}
