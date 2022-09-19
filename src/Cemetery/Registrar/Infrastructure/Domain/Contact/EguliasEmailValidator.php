<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\Contact;

use Cemetery\Registrar\Domain\Model\Contact\EmailValidator as EmailValidatorInterface;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EguliasEmailValidator implements EmailValidatorInterface
{
    public function __construct(
        private EmailValidator $emailValidator,
    ) {}

    public function isValid(string $email): bool
    {
        return $this->emailValidator->isValid($email, new RFCValidation());
    }
}
