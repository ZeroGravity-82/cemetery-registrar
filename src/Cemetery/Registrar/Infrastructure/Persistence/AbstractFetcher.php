<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence;

use Cemetery\Registrar\Domain\View\FetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractFetcher implements FetcherInterface
{
    protected function isTermNotEmpty(?string $term): bool
    {
        return $term !== null && $term !== '';
    }
}
