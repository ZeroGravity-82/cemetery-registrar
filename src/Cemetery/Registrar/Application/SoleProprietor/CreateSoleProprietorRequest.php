<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\SoleProprietor;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateSoleProprietorRequest
{
    /**
     * @param string      $name
     * @param string|null $inn
     * @param string|null $ognip
     * @param string|null $okpo
     * @param string|null $okved
     * @param string|null $registrationAddress
     * @param string|null $actualLocationAddress
     * @param string|null $bankName
     * @param string|null $bik
     * @param string|null $correspondentAccount
     * @param string|null $currentAccount
     * @param string|null $phone
     * @param string|null $phoneAdditional
     * @param string|null $fax
     * @param string|null $email
     * @param string|null $website
     */
    public function __construct(
        public string  $name,
        public ?string $inn,
        public ?string $ognip,
        public ?string $okpo,
        public ?string $okved,
        public ?string $registrationAddress,
        public ?string $actualLocationAddress,
        public ?string $bankName,
        public ?string $bik,
        public ?string $correspondentAccount,
        public ?string $currentAccount,
        public ?string $phone,
        public ?string $phoneAdditional,
        public ?string $fax,
        public ?string $email,
        public ?string $website,
    ) {}
}
