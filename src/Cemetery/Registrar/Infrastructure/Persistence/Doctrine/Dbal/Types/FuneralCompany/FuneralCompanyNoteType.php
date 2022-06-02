<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyNote;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyNoteType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = FuneralCompanyNote::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'funeral_company_note';
}
