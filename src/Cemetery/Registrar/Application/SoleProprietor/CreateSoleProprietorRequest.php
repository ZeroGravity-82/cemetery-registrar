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
        public readonly string  $name,
        public readonly ?string $inn,
        public readonly ?string $ognip,
        public readonly ?string $okpo,
        public readonly ?string $okved,
        public readonly ?string $registrationAddress,
        public readonly ?string $actualLocationAddress,
        public readonly ?string $bankName,
        public readonly ?string $bik,
        public readonly ?string $correspondentAccount,
        public readonly ?string $currentAccount,
        public readonly ?string $phone,
        public readonly ?string $phoneAdditional,
        public readonly ?string $fax,
        public readonly ?string $email,
        public readonly ?string $website,
    ) {}
}
