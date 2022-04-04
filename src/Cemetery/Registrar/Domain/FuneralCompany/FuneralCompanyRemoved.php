<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\AbstractEvent;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyRemoved extends AbstractEvent
{
    /**
     * @param FuneralCompanyId $funeralCompanyId
     */
    public function __construct(
        private FuneralCompanyId $funeralCompanyId,
    ) {
        parent::__construct();
    }

    /**
     * @return FuneralCompanyId
     */
    public function getFuneralCompanyId(): FuneralCompanyId
    {
        return $this->funeralCompanyId;
    }
}
