<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial;

use Cemetery\Registrar\Domain\Burial\BurialRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class BurialService
{
    /**
     * @param BurialRepository $burialRepo
     */
    public function __construct(
        protected readonly BurialRepository $burialRepo,
    ) {}
}
