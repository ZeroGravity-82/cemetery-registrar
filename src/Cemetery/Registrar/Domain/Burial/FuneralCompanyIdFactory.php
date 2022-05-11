<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyIdFactory
{
    /**
     * @param SoleProprietorId|JuristicPersonId $id
     *
     * @return FuneralCompanyId
     */
    public function create(SoleProprietorId|JuristicPersonId $id): FuneralCompanyId
    {
        return new FuneralCompanyId($id);
    }

    /**
     * @param string|null $id
     *
     * @return FuneralCompanyId
     */
    public function createForSoleProprietor(?string $id): FuneralCompanyId
    {
        return new FuneralCompanyId(new SoleProprietorId((string) $id));
    }

    /**
     * @param string|null $id
     *
     * @return FuneralCompanyId
     */
    public function createForJuristicPerson(?string $id): FuneralCompanyId
    {
        return new FuneralCompanyId(new JuristicPersonId((string) $id));
    }
}
