<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface IdentityGeneratorInterface
{
    /**
     * @return string
     */
    public function getNextIdentity(): string;
}
