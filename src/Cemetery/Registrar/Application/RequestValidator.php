<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RequestValidator
{
    /**
     * @throw \InvalidArgumentException when the entity ID is not provided or empty
     */
    protected function assertValidEntityId(?string $id): void
    {
        if ($id === null || empty(\trim($id))) {
            throw new \InvalidArgumentException('Идентификатор доменной сущности не задан или пуст.');
        }
    }
}