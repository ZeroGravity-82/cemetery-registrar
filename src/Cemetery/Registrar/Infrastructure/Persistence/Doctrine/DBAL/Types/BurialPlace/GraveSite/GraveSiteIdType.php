<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class GraveSiteIdType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = GraveSiteId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'grave_site_id';
}
