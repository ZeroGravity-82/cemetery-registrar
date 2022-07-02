<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson\Exception;

/**
 * Exceptions for the natural person entity.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonException extends \Exception
{
    public const BIRTHDATE_FOLLOWS_DEATH_DATE           = 'Дата рождения не может следовать за датой смерти.';
    public const DEATH_DATE_PRECEDES_BIRTHDATE          = 'Дата смерти не может предшествовать дате рождения.';
    public const AGE_FOR_BOTH_BIRTH_AND_DEATH_DATES_SET = 'Возраст не может быть задан, т.к. уже заданы даты рождения и смерти.';

    /**
     * @return self
     */
    public static function birthdateFollowsDeathDate(): self
    {
        return new self(self::BIRTHDATE_FOLLOWS_DEATH_DATE);
    }

    /**
     * @return self
     */
    public static function deathDatePrecedesBirthdate(): self
    {
        return new self(self::DEATH_DATE_PRECEDES_BIRTHDATE);
    }

    /**
     * @return self
     */
    public static function ageForBothBirthAndDeathDatesSet(): self
    {
        return new self(self::AGE_FOR_BOTH_BIRTH_AND_DEATH_DATES_SET);
    }
}
