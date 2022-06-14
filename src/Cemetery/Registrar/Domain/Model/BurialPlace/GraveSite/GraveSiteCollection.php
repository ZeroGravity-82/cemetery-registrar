<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\EntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteCollection extends EntityCollection
{
    /**
     * {@inheritdoc}
     */
    public function supportedEntityClassName(): string
    {
        return GraveSite::class;
    }
}
