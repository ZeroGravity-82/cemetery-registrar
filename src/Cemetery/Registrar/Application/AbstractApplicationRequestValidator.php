<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractApplicationRequestValidator
{
    protected Notification $note;

    public function __construct() {
        $this->note = new Notification();
    }

    abstract public function validate(AbstractApplicationRequest $request): Notification;

    protected function note(): Notification
    {
        return $this->note;
    }

    protected function validateId(AbstractApplicationRequest $request): self
    {
        if (\property_exists($request, 'id') && ($request->id === null || empty(\trim($request->id)))) {
            $this->note->addError('id', 'Идентификатор доменной сущности не указан.');
        }

        return $this;
    }

    protected function doesDateTimeStringHasValidFormat(string $value): bool
    {
        return \preg_match('~\d{4}-\d{2}-\d{2}~', $value) === 1;
    }
}
