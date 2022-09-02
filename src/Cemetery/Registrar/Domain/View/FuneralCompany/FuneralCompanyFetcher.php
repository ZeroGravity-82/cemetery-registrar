<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\FuneralCompany;

use Cemetery\Registrar\Domain\View\Fetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface FuneralCompanyFetcher extends Fetcher
{
    /**
     * Checks if the funeral company exists by the name.
     */
    public function doesExistByName(string $name): bool;
}
