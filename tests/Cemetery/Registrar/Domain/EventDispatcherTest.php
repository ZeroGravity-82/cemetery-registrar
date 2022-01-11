<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain;

use Cemetery\Registrar\Domain\AbstractEvent;
use Cemetery\Registrar\Domain\EventInterface;
use Cemetery\Registrar\Domain\EventDispatcher;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <nikolay.ryabkov@sibers.com>
 */
class EventDispatcherTest extends TestCase
{
    public function testItAddsListeners(): void
    {
        $dispatcher = new EventDispatcher();
        $callableA1 = function () {};
        $callableA2 = function () {};
        $callableA3 = function () {};
        $eventA     = new class {};
        $callableB1 = function () {};
        $eventB     = new class {};
        $dispatcher->addListener($eventA::class, $callableA1);
        $dispatcher->addListener($eventA::class, $callableA2);
        $dispatcher->addListener($eventA::class, $callableA3);
        $dispatcher->addListener($eventB::class, $callableB1);

        $this->assertIsArray($dispatcher->getListenersForEvent($eventA));
        $this->assertCount(3, $dispatcher->getListenersForEvent($eventA));
        $this->assertContains($callableA1, $dispatcher->getListenersForEvent($eventA));
        $this->assertContains($callableA2, $dispatcher->getListenersForEvent($eventA));
        $this->assertContains($callableA3, $dispatcher->getListenersForEvent($eventA));

        $this->assertIsArray($dispatcher->getListenersForEvent($eventB));
        $this->assertCount(1, $dispatcher->getListenersForEvent($eventB));
        $this->assertContains($callableB1, $dispatcher->getListenersForEvent($eventB));
    }

    public function testItRemovesListeners(): void
    {
        $dispatcher = new EventDispatcher();
        $callableA1 = function () {};
        $callableA2 = function () {};
        $callableA3 = function () {};
        $eventA     = new class {};
        $dispatcher->addListener($eventA::class, $callableA1);
        $dispatcher->addListener($eventA::class, $callableA2);
        $dispatcher->addListener($eventA::class, $callableA3);
        $dispatcher->removeListener($eventA::class, $callableA2);

        $this->assertIsArray($dispatcher->getListenersForEvent($eventA));
        $this->assertCount(2, $dispatcher->getListenersForEvent($eventA));
        $this->assertContains($callableA1, $dispatcher->getListenersForEvent($eventA));
        $this->assertContains($callableA3, $dispatcher->getListenersForEvent($eventA));
        $this->assertNotContains($callableA2, $dispatcher->getListenersForEvent($eventA));
    }

    public function testItFailsToAddTheSameEventListenerTwice(): void
    {
        $dispatcher = new EventDispatcher();
        $callable   = function () {};
        $event      = new class {};
        $dispatcher->addListener($event::class, $callable);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            \sprintf('This event listener of type "Closure" for event %s already added.', $event::class)
        );
        $dispatcher->addListener($event::class, $callable);
    }

    public function testItDispatchesEvents(): void
    {
        $dispatcher = new EventDispatcher();
        $eventA     = new class {};
        $eventB     = new class {};
        $eventC     = new class {};
        $eventD     = new class {};
        $eventE     = new class {};

        $mockWithListeners = $this->getMockBuilder(\stdClass::class)
            ->addMethods([
                'onEventA',
                'onEventB',
                'onEventC',
                'onEventD',
                'onEventE',
            ])
            ->getMock();
        $dispatcher->addListener($eventA::class, [$mockWithListeners, 'onEventA']);
        $dispatcher->addListener($eventB::class, [$mockWithListeners, 'onEventB']);
        $dispatcher->addListener($eventC::class, [$mockWithListeners, 'onEventC']);
        $dispatcher->addListener($eventD::class, [$mockWithListeners, 'onEventD']);
        $dispatcher->addListener($eventE::class, [$mockWithListeners, 'onEventE']);

        $mockWithListeners->expects($this->once())->method('onEventA');
        $dispatcher->dispatch($eventA);

        $mockWithListeners->expects($this->once())->method('onEventB');
        $mockWithListeners->expects($this->once())->method('onEventC');
        $dispatcher->dispatch($eventB, $eventC);

        $mockWithListeners->expects($this->once())->method('onEventD');
        $mockWithListeners->expects($this->once())->method('onEventE');
        $dispatcher->dispatch(...[$eventD, $eventE]);
    }

    public function testItInvokesListenerOnChildEvent(): void
    {
        $dispatcher = new EventDispatcher();
        $childEvent = new class extends AbstractEvent {};

        $mockWithListeners = $this->getMockBuilder(\stdClass::class)
            ->addMethods([
                'onAnyDomainEvent',
            ])
            ->getMock();
        $dispatcher->addListener(EventInterface::class, [$mockWithListeners, 'onAnyDomainEvent']);

        $mockWithListeners->expects($this->once())->method('onAnyDomainEvent');
        $dispatcher->dispatch($childEvent);
    }

    public function testItReturnsListenersForParentEvent(): void
    {
        $dispatcher  = new EventDispatcher();
        $childEventA = new class extends AbstractEvent {};
        $childEventB = new class extends AbstractEvent {};

        $mockWithListeners = $this->getMockBuilder(\stdClass::class)
            ->addMethods([
                'onChildEventA',
                'onChildEventB',
                'onAnyDomainEvent',
            ])
            ->getMock();
        $dispatcher->addListener($childEventA::class, [$mockWithListeners, 'onChildEventA']);
        $dispatcher->addListener($childEventB::class, [$mockWithListeners, 'onChildEventB']);
        $dispatcher->addListener(EventInterface::class, [$mockWithListeners, 'onAnyDomainEvent']);

        $this->assertCount(2, $dispatcher->getListenersForEvent($childEventA));
    }
}
