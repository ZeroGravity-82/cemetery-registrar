<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface TransactionalSession
{
   public function executeAtomically(callable $operation): mixed;
}
