<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Identity;

use Cemetery\Registrar\Domain\IdentityGenerator;
use Ramsey\Uuid\UuidFactory;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RamseyIdentityGenerator implements IdentityGenerator
{
    /**
     * @param UuidFactory $uuidFactory
     */
    public function __construct(
        private readonly UuidFactory $uuidFactory,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function getNextIdentity(): string
    {
        return $this->uuidFactory->uuid4()->toString();
    }
}
