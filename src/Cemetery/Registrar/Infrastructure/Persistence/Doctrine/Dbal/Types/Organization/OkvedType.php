<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization;

use Cemetery\Registrar\Domain\Model\Organization\Okved;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OkvedType extends AbstractCustomStringType
{
    protected string $className = Okved::class;
    protected string $typeName  = 'okved';
}
