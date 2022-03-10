<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\DeleteFuneralCompany;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DeleteFuneralCompanyRequest
{
    /**
     * @param string $funeralCompanyId
     */
    public function __construct(
        public string $funeralCompanyId,
    ) {}
}
