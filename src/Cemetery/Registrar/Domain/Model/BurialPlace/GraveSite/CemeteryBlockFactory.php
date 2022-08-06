<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\EntityFactory;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockFactory extends EntityFactory
{
    /**
     * @throws Exception when generating an invalid cemetery block ID
     * @throws Exception when the name is invalid
     */
    public function create(
        ?string $name,
    ): CemeteryBlock {
        $name = new CemeteryBlockName((string) $name);

        return (new CemeteryBlock(
            new CemeteryBlockId($this->identityGenerator->getNextIdentity()),
            $name,
        ));
    }
}
