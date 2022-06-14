<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\FuneralCompany;

use Cemetery\Registrar\Domain\Model\EntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyCollection extends EntityCollection
{
    /**
     * {@inheritdoc}
     */
    public function supportedEntityClassName(): string
    {
        return FuneralCompany::class;
    }
}
