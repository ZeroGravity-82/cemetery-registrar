<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain;

use Cemetery\Registrar\Domain\Event;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractEventTest extends TestCase
{
    protected Event $event;

    public function testItReturnsDateTimeOfOccurrence(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->event->createdAt());
    }
}
