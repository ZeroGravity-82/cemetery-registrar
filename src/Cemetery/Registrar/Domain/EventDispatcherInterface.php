<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface EventDispatcherInterface
{
    /**
     * Adds the listener to an iterable (array, iterator, or generator) of callables that listen for the event. The
     * listener callable must be type-compatible with the event.
     *
     * @param string   $eventClass
     * @param callable $listener
     *
     * @throws \LogicException when the event listener of the same type has already been added
     */
    public function addListener(string $eventClass, callable $listener): void;

    /**
     * Removes the listener from an iterable (array, iterator, or generator) of callables that listen for the event.
     *
     * @param string   $eventClass
     * @param callable $listener
     */
    public function removeListener(string $eventClass, callable $listener): void;

    /**
     * Returns listeners relevant to the event.
     *
     * @param object $event        An event for which to return the relevant listeners.
     *
     * @return iterable[callable]  An iterable (array, iterator, or generator) of callables.
     */
    public function getListenersForEvent(object $event): iterable;

    /**
     * Provides all relevant listeners with the events to process.
     *
     * @param object ...$events The events to process.
     */
    public function dispatch(object ...$events): void;
}
