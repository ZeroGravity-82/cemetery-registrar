<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonContact;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyNaturalPersonContactRequest extends ApplicationRequest
{
    public function __construct(
        public ?string $id,
        public ?string $phone,
        public ?string $phoneAdditional,
        public ?string $address,
        public ?string $email,
    ) {}
}
