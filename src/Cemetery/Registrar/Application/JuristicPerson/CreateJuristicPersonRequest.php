<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateJuristicPersonRequest
{
    /**
     * @param string      $juristicPersonName
     * @param string|null $juristicPersonInn
     * @param string|null $juristicPersonKpp
     * @param string|null $juristicPersonOgrn
     * @param string|null $juristicPersonOkpo
     * @param string|null $juristicPersonOkved
     * @param string|null $juristicPersonLegalAddress
     * @param string|null $juristicPersonPostalAddress
     * @param string|null $juristicPersonBankName
     * @param string|null $juristicPersonBik
     * @param string|null $juristicPersonCorrespondentAccount
     * @param string|null $juristicPersonCurrentAccount
     * @param string|null $juristicPersonPhone
     * @param string|null $juristicPersonPhoneAdditional
     * @param string|null $juristicPersonFax
     * @param string|null $juristicPersonGeneralDirector
     * @param string|null $juristicPersonEmail
     * @param string|null $juristicPersonWebsite
     */
    public function __construct(
        public string  $juristicPersonName,
        public ?string $juristicPersonInn,
        public ?string $juristicPersonKpp,
        public ?string $juristicPersonOgrn,
        public ?string $juristicPersonOkpo,
        public ?string $juristicPersonOkved,
        public ?string $juristicPersonLegalAddress,
        public ?string $juristicPersonPostalAddress,
        public ?string $juristicPersonBankName,
        public ?string $juristicPersonBik,
        public ?string $juristicPersonCorrespondentAccount,
        public ?string $juristicPersonCurrentAccount,
        public ?string $juristicPersonPhone,
        public ?string $juristicPersonPhoneAdditional,
        public ?string $juristicPersonFax,
        public ?string $juristicPersonGeneralDirector,
        public ?string $juristicPersonEmail,
        public ?string $juristicPersonWebsite,
    ) {}
}
