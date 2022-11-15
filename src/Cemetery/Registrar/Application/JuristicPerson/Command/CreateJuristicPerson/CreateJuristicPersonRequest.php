<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson\Command\CreateJuristicPerson;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateJuristicPersonRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $name,
        public ?string $inn,
        public ?string $kpp,
        public ?string $ogrn,
        public ?string $okpo,
        public ?string $okved,
        public ?string $legalAddress,
        public ?string $postalAddress,
        public ?string $bankDetailsBankName,
        public ?string $bankDetailsBik,
        public ?string $bankDetailsCorrespondentAccount,
        public ?string $bankDetailsCurrentAccount,
        public ?string $phone,
        public ?string $phoneAdditional,
        public ?string $fax,
        public ?string $generalDirector,
        public ?string $email,
        public ?string $website,
    ) {}
}
