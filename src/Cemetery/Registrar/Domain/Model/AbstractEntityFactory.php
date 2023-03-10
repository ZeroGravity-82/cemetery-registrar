<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractEntityFactory
{
    public function __construct(
        protected IdentityGeneratorInterface $identityGenerator,
    ) {}
}
