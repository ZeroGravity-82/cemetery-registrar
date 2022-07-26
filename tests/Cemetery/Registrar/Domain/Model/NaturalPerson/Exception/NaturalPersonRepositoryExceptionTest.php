<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson\Exception;

use Cemetery\Registrar\Domain\Model\NaturalPerson\Exception\NaturalPersonRepositoryException;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonRepositoryExceptionTest extends TestCase
{
    public const SAME_FULL_NAME_AND_BORN_AT_OR_DIED_AT_ALREADY_USED = 'Физлицо с таким ФИО и такой датой рождения или датой смерти уже существует.';

    public function testItHasValidMessageConstants(): void
    {
        $this->assertSame(
            self::SAME_FULL_NAME_AND_BORN_AT_OR_DIED_AT_ALREADY_USED,
            NaturalPersonRepositoryException::SAME_FULL_NAME_AND_BORN_AT_OR_DIED_AT_ALREADY_USED
        );
    }

    public function testItReturnsExceptionInstanceForSameFullNameAndBornAtOrDiedAtAlreadyUsed(): void
    {
        $exception = NaturalPersonRepositoryException::sameFullNameAndBornAtOrDiedAtAlreadyUsed();
        $this->assertInstanceOf(NaturalPersonRepositoryException::class, $exception);
        $this->assertSame(self::SAME_FULL_NAME_AND_BORN_AT_OR_DIED_AT_ALREADY_USED, $exception->getMessage());
    }
}
