<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Command;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface TransactionalSession
{
    /**
     * @param callable $operation
     *
     * @return mixed
     */
   public function executeAtomically(callable $operation): mixed;
}
