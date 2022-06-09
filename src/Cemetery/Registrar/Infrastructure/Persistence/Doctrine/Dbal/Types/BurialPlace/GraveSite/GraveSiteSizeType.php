<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteSizeType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = GraveSiteSize::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'grave_site_size';
}
