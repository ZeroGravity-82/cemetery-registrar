<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Identity;

use Cemetery\Registrar\Domain\Model\IdentityGeneratorInterface;
use Ramsey\Uuid\UuidFactory;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RamseyIdentityGenerator implements IdentityGeneratorInterface
{
    public function __construct(
        private UuidFactory $uuidFactory,
    ) {}

    public function getNextIdentity(): string
    {
        return $this->uuidFactory->uuid4()->toString();
    }
}
