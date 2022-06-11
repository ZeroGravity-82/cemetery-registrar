<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Application\Query;

use Doctrine\DBAL\Connection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class Fetcher
{
    /**
     * @param Connection $connection
     */
    public function __construct(
        protected readonly Connection $connection,
    ) {}
}
