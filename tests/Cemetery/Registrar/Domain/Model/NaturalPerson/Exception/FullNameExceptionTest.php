<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson\Exception;

use Cemetery\Registrar\Domain\Model\NaturalPerson\Exception\FullNameException;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FullNameExceptionTest extends TestCase
{
    private const EMPTY_FULL_NAME = 'ФИО не может иметь пустое значение.';

    public function testItHasValidMessageConstants(): void
    {
        $this->assertSame(self::EMPTY_FULL_NAME, FullNameException::EMPTY_FULL_NAME);
    }

    public function testItReturnsExceptionInstanceForEmptyFullName(): void
    {
        $exception = FullNameException::emptyFullName();
        $this->assertInstanceOf(FullNameException::class, $exception);
        $this->assertSame(self::EMPTY_FULL_NAME, $exception->getMessage());
    }
}
