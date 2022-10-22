<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EntityFactory
{
    public function __construct(
        protected readonly IdentityGenerator $identityGenerator,
    ) {}
}
