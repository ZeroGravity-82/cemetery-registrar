<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson\Exception;

/**
 * Exceptions for a full name of a natural person.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FullNameException extends \Exception
{
    public const EMPTY_FULL_NAME = 'ФИО не может иметь пустое значение.';

    /**
     * @return self
     */
    public static function emptyFullName(): self
    {
        return new self(self::EMPTY_FULL_NAME);
    }
}
