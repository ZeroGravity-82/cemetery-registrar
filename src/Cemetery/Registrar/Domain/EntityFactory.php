<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EntityFactory
{
    /**
     * @param IdentityGenerator $identityGenerator
     */
    public function __construct(
        protected readonly IdentityGenerator $identityGenerator,
    ) {}
}
