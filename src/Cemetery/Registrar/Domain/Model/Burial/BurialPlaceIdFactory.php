<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceIdFactory
{
    public function create(GraveSiteId|ColumbariumNicheId|MemorialTreeId $id): BurialPlaceId
    {
        return new BurialPlaceId($id);
    }

    /**
     * @throws Exception when the ID is invalid
     */
    public function createForGraveSite(?string $id): BurialPlaceId
    {
        return new BurialPlaceId(new GraveSiteId((string) $id));
    }

    /**
     * @throws Exception when the ID is invalid
     */
    public function createForColumbariumNiche(?string $id): BurialPlaceId
    {
        return new BurialPlaceId(new ColumbariumNicheId((string) $id));
    }

    /**
     * @throws Exception when the ID is invalid
     */
    public function createForMemorialTree(?string $id): BurialPlaceId
    {
        return new BurialPlaceId(new MemorialTreeId((string) $id));
    }
}
