<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson\Exception;

use Cemetery\Registrar\Domain\Model\NaturalPerson\Exception\NaturalPersonException;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonExceptionTest extends TestCase
{
    public const BIRTHDATE_FOLLOWS_DEATH_DATE           = 'Дата рождения не может следовать за датой смерти.';
    public const DEATH_DATE_PRECEDES_BIRTHDATE          = 'Дата смерти не может предшествовать дате рождения.';
    public const AGE_FOR_BOTH_BIRTH_AND_DEATH_DATES_SET = 'Возраст не может быть задан, т.к. уже заданы даты рождения и смерти.';

    public function testItHasValidMessageConstants(): void
    {
        $this->assertSame(self::BIRTHDATE_FOLLOWS_DEATH_DATE,           NaturalPersonException::BIRTHDATE_FOLLOWS_DEATH_DATE);
        $this->assertSame(self::DEATH_DATE_PRECEDES_BIRTHDATE,          NaturalPersonException::DEATH_DATE_PRECEDES_BIRTHDATE);
        $this->assertSame(self::AGE_FOR_BOTH_BIRTH_AND_DEATH_DATES_SET, NaturalPersonException::AGE_FOR_BOTH_BIRTH_AND_DEATH_DATES_SET);
    }

    public function testItReturnsExceptionInstanceForBirthdateFollowsDeathDate(): void
    {
        $exception = NaturalPersonException::birthdateFollowsDeathDate();
        $this->assertInstanceOf(NaturalPersonException::class, $exception);
        $this->assertSame(self::BIRTHDATE_FOLLOWS_DEATH_DATE, $exception->getMessage());
    }

    public function testItReturnsExceptionInstanceForDeathDatePrecedesBirthdate(): void
    {
        $exception = NaturalPersonException::deathDatePrecedesBirthdate();
        $this->assertInstanceOf(NaturalPersonException::class, $exception);
        $this->assertSame(self::DEATH_DATE_PRECEDES_BIRTHDATE, $exception->getMessage());
    }

    public function testItReturnsExceptionInstanceForAgeForBothBirthAndDeathDatesSet(): void
    {
        $exception = NaturalPersonException::ageForBothBirthAndDeathDatesSet();
        $this->assertInstanceOf(NaturalPersonException::class, $exception);
        $this->assertSame(self::AGE_FOR_BOTH_BIRTH_AND_DEATH_DATES_SET, $exception->getMessage());
    }
}
