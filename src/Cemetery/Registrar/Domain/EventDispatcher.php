<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var callable[][]|array
     */
    private array $listeners = [];

    /**
     * {@inheritdoc}
     */
    public function addListener(string $eventClass, callable $listener): void
    {
        if (!isset($this->listeners[$eventClass])) {
            $this->listeners[$eventClass] = [];
        }
        if (in_array($listener, $this->listeners[$eventClass])) {
            throw new \LogicException(
                \sprintf(
                    'This event listener of type "%s" for event %s already added.',
                    get_debug_type($listener),
                    $eventClass
                )
            );
        }
        $this->listeners[$eventClass][] = $listener;
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener(string $eventClass, callable $listener): void
    {
        if (!isset($this->listeners[$eventClass])) {
            return;
        }
        $key = \array_search($listener, $this->listeners[$eventClass]);
        if ($key !== false) {
            unset($this->listeners[$eventClass][$key]);
            $this->listeners[$eventClass] = \array_values($this->listeners[$eventClass]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event): array
    {
        $listenersForEvent = [];
        foreach ($this->listeners as $eventClass => $listeners) {
            if (!$event instanceof $eventClass) {
                continue;
            }
            foreach ($listeners as $listener) {
                $listenersForEvent[] = $listener;
            }
        }

        return $listenersForEvent;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(object ...$events): void
    {
        foreach ($events as $event) {
            foreach ($this->getListenersForEvent($event) as $listener) {
                $listener($event);
            }
        }
    }
}
