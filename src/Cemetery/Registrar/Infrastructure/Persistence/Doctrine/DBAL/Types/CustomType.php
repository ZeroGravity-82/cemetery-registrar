<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class CustomType extends Type
{
    /**
     * @var string
     */
    protected string $className;

    /**
     * @var string
     */
    protected string $typeName;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->typeName;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
