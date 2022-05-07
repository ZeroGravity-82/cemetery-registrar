<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Contact;

use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class PhoneNumberType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = PhoneNumber::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'phone_number';
}