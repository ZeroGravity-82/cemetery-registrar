<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Contact;

use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class WebsiteType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Website::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'website';
}
