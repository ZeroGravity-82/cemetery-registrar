<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\EntityMaskingId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTreeId;

/**
 * Wrapper class for burial place ID value objects.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialPlaceId extends EntityMaskingId
{
    /**
     * @param GraveSiteId|ColumbariumNicheId|MemorialTreeId $id
     */
    public function __construct(
        GraveSiteId|ColumbariumNicheId|MemorialTreeId $id
    ) {
        parent::__construct($id);
    }
}
