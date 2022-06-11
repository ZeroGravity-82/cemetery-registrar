<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Doctrine\DBAL\Connection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class DoctrineDbalFetcher
{
    /**
     * @param Connection $connection
     */
    public function __construct(
        protected readonly Connection $connection,
    ) {}
}
