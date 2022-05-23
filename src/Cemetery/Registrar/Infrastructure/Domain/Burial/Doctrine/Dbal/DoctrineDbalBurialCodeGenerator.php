<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\Dbal;

use Cemetery\Registrar\Domain\Burial\BurialCodeGenerator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DoctrineDbalBurialCodeGenerator implements BurialCodeGenerator
{
    /**
     * {@inheritdoc}
     */
    public function getNextCode(): string
    {
        return '000000000';
    }
}
