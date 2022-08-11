<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\AggregateRoot;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlock extends AggregateRoot
{
    public function __construct(
        private CemeteryBlockId   $id,
        private CemeteryBlockName $name,
    ) {
        parent::__construct();
    }

    public function id(): CemeteryBlockId
    {
        return $this->id;
    }

    public function name(): CemeteryBlockName
    {
        return $this->name;
    }

    public function setName(CemeteryBlockName $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function createGraveSite(
        int     $rowInBlock,
        ?int    $positionInRow,
        ?string $geoPositionLatitude,
        ?string $geoPositionLongitude,
        ?string $geoPositionError,
        ?string $size,
    ): GraveSite {
        // TODO implement creation
    }
}
