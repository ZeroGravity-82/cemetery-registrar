<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\SoleProprietor\Command\CreateSoleProprietor;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateSoleProprietorRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $name,
        public ?string $inn,
        public ?string $ogrnip,
        public ?string $okpo,
        public ?string $okved,
        public ?string $registrationAddress,
        public ?string $actualLocationAddress,
        public ?string $bankDetailsBankName,
        public ?string $bankDetailsBik,
        public ?string $bankDetailsCorrespondentAccount,
        public ?string $bankDetailsCurrentAccount,
        public ?string $phone,
        public ?string $phoneAdditional,
        public ?string $fax,
        public ?string $email,
        public ?string $website,
    ) {}
}
