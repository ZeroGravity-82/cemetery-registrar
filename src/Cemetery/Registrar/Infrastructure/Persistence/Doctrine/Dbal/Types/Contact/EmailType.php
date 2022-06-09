<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Contact;

use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EmailType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Email::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'email';
}
