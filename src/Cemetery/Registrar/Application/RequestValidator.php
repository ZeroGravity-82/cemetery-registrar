<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RequestValidator
{
    /**
     * @throw \InvalidArgumentException when the ID is null
     */
    protected function assertValidEntityId(?string $id): void
    {
        if ($id === null) {
            throw new \InvalidArgumentException('Идентификатор доменной сущности не указан.');
        }
    }
}