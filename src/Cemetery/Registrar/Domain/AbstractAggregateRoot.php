<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractAggregateRoot extends Entity
{
    /**
     * @var EventInterface[]|array
     */
    private array $recordedEvents = [];

    /**
     * Returns recorded domain events and removes them from the aggregate root.
     *
     * @return array
     */
    public function releaseRecordedEvents(): array
    {
        $recordedEvents       = $this->recordedEvents;
        $this->recordedEvents = [];

        return $recordedEvents;
    }

    /**
     * Records the domain event of the aggregate root for subsequent release.
     *
     * @param EventInterface $event
     *
     * @throws \LogicException when the same event has already been recorded
     */
    protected function recordEvent(EventInterface $event): void
    {
        if (\in_array($event, $this->recordedEvents)) {
            throw new \LogicException(
                \sprintf('The same domain event of type "%s" already recorded.', \get_class($event))
            );
        }
        $this->recordedEvents[] = $event;
    }
}
