<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain;

use Cemetery\Registrar\Domain\IdentityGeneratorInterface;
use Ramsey\Uuid\UuidFactory;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class RamseyIdentityGenerator implements IdentityGeneratorInterface
{
    /**
     * @param UuidFactory $uuidFactory
     */
    public function __construct(
        private UuidFactory $uuidFactory,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function getNextIdentity(): string
    {
        return $this->uuidFactory->uuid4()->toString();
    }
}
