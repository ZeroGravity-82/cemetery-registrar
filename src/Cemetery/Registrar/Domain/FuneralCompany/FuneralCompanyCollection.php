<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\AbstractEntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyCollection extends AbstractEntityCollection
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedEntityClass(): string
    {
        return FuneralCompany::class;
    }
}
