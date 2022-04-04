<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class RemoveFuneralCompanyRequest
{
    /**
     * @param string $funeralCompanyId
     */
    public function __construct(
        public string $funeralCompanyId,
    ) {}
}
