<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson\Exception;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * Exceptions for the natural person entity.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonRepositoryException extends Exception
{
    public const SAME_FULL_NAME_AND_BORN_AT_OR_DIED_AT_ALREADY_USED = 'Физлицо с таким ФИО и такой датой рождения или датой смерти уже существует.';

    /**
     * @return self
     */
    public static function sameFullNameAndBornAtOrDiedAtAlreadyUsed(): self
    {
        return new self(self::SAME_FULL_NAME_AND_BORN_AT_OR_DIED_AT_ALREADY_USED);
    }
}
