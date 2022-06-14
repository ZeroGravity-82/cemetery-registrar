<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model;

use Cemetery\Registrar\Domain\Model\Event;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EventTest extends TestCase
{
    protected Event $event;

    public function testItReturnsDateTimeOfOccurrence(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->event->createdAt());
    }
}
