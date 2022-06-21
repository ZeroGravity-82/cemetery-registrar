<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class Fetcher
{
    /**
     * @param string|null $term
     *
     * @return bool
     */
    protected function isTermNotEmpty(?string $term): bool
    {
        return $term !== null && $term !== '';
    }
}
