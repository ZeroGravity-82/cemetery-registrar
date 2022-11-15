<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceIdFactory
{
    public function create(GraveSiteId|ColumbariumNicheId|MemorialTreeId $id): AbstractBurialPlaceId
    {
        return new AbstractBurialPlaceId($id);
    }

    /**
     * @throws Exception when the ID is invalid
     */
    public function createForGraveSite(?string $id): AbstractBurialPlaceId
    {
        return new AbstractBurialPlaceId(new GraveSiteId((string) $id));
    }

    /**
     * @throws Exception when the ID is invalid
     */
    public function createForColumbariumNiche(?string $id): AbstractBurialPlaceId
    {
        return new AbstractBurialPlaceId(new ColumbariumNicheId((string) $id));
    }

    /**
     * @throws Exception when the ID is invalid
     */
    public function createForMemorialTree(?string $id): AbstractBurialPlaceId
    {
        return new AbstractBurialPlaceId(new MemorialTreeId((string) $id));
    }
}
