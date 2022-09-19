<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Contact;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface EmailValidator
{
    public function isValid(string $email): bool;
}
