<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface IdentityGeneratorInterface
{
    public function getNextIdentity(): string;
}