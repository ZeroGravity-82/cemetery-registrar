<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface BurialCodeGeneratorInterface
{
    /**
     * @return string
     */
    public function getNextCode(): string;
}
