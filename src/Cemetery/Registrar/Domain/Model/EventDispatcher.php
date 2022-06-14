<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EventDispatcher
{
    /**
     * @var callable[][]|array
     */
    private array $listeners = [];

    /**
     * Adds the listener to the array of callables that listen for the event. The listener callable must be
     * type-compatible with the event.
     *
     * @param string   $eventClass
     * @param callable $listener
     *
     * @throws \LogicException when the event listener of the same type has already been added
     */
    public function addListener(string $eventClass, callable $listener): void
    {
        if (!isset($this->listeners[$eventClass])) {
            $this->listeners[$eventClass] = [];
        }
        if (\in_array($listener, $this->listeners[$eventClass])) {
            throw new \LogicException(
                \sprintf(
                    'Слушатель с типом "%s" для события "%s" уже добавлен.',
                    \get_debug_type($listener),
                    $eventClass
                )
            );
        }
        $this->listeners[$eventClass][] = $listener;
    }

    /**
     * Removes the listener from the array of callables that listen for the event.
     *
     * @param string   $eventClass
     * @param callable $listener
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
     * Returns listeners relevant to the event.
     *
     * @param object $event    An event for which to return the relevant listeners.
     *
     * @return array[callable]
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
     * Provides all relevant listeners with the events to process.
     *
     * @param object ...$events The events to process.
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
