<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Contact;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class AddressType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Address::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'address';
}
