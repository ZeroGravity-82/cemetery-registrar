<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface EventInterface
{
    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable;
}
