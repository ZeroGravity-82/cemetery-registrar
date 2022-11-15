<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Registrar\Domain\Model\AbstractAggregateRoot;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Columbarium extends AbstractAggregateRoot
{
    private ?GeoPosition $geoPosition = null;

    public function __construct(
        private ColumbariumId   $id,
        private ColumbariumName $name,
    ) {
        parent::__construct();
    }

    public function id(): ColumbariumId
    {
        return $this->id;
    }

    public function name(): ColumbariumName
    {
        return $this->name;
    }

    public function setName(ColumbariumName $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function geoPosition(): ?GeoPosition
    {
        return $this->geoPosition;
    }

    public function setGeoPosition(?GeoPosition $geoPosition): self
    {
        $this->geoPosition = $geoPosition;

        return $this;
    }
}
