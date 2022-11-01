<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence;

use Cemetery\Registrar\Domain\View\Fetcher as FetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class Fetcher implements FetcherInterface
{
    protected function isTermNotEmpty(?string $term): bool
    {
        return $term !== null && $term !== '';
    }
}
