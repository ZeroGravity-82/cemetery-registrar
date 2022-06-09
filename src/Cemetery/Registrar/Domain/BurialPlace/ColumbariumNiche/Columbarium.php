<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\AggregateRoot;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Columbarium extends AggregateRoot
{
    /**
     * @var GeoPosition|null
     */
    private ?GeoPosition $geoPosition = null;

    /**
     * @param ColumbariumId   $id
     * @param ColumbariumName $name
     */
    public function __construct(
        private readonly ColumbariumId $id,
        private ColumbariumName        $name,
    ) {
        parent::__construct();
    }

    /**
     * @return ColumbariumId
     */
    public function id(): ColumbariumId
    {
        return $this->id;
    }

    /**
     * @return ColumbariumName
     */
    public function name(): ColumbariumName
    {
        return $this->name;
    }

    /**
     * @param ColumbariumName $name
     *
     * @return $this
     */
    public function setName(ColumbariumName $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return GeoPosition|null
     */
    public function geoPosition(): ?GeoPosition
    {
        return $this->geoPosition;
    }

    /**
     * @param GeoPosition|null $geoPosition
     *
     * @return $this
     */
    public function setGeoPosition(?GeoPosition $geoPosition): self
    {
        $this->geoPosition = $geoPosition;

        return $this;
    }
}
