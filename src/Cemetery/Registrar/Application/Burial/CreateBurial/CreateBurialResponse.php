<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\CreateBurial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateBurialResponse
{
    /**
     * @param string $burialId
     */
    public function __construct(
        public string $burialId,
    ) {}
}