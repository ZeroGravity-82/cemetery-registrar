<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheIdType extends AbstractCustomStringType
{
    protected string $className = ColumbariumNicheId::class;
    protected string $typeName  = 'columbarium_niche_id';
}
