<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface BurialCodeGenerator
{
    /**
     * @return string
     */
    public function getNextCode(): string;
}
