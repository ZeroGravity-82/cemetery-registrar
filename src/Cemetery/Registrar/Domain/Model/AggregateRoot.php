<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AggregateRoot extends Entity
{
    /**
     * @var Event[]|array
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
     * @param Event $event
     *
     * @throws \LogicException when the same event has already been recorded
     */
    protected function recordEvent(Event $event): void
    {
        if (\in_array($event, $this->recordedEvents)) {
            throw new \LogicException(
                \sprintf('Доменное событие с типом "%s" уже зарегистрировано.', \get_class($event))
            );
        }
        $this->recordedEvents[] = $event;
    }
}
