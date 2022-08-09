<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class ApplicationRequestValidator
{
    protected Notification $note;

    public function __construct() {
        $this->note = new Notification();
    }

    abstract public function validate(ApplicationRequest $request): Notification;

    protected function note(): Notification
    {
        return $this->note;
    }

    protected function validateId(ApplicationRequest $request): self
    {
        if (\property_exists($request, 'id') && ($request->id === null || empty(\trim($request->id)))) {
            $this->note->addError('id', 'Идентификатор доменной сущности не указан.');
        }

        return $this;
    }
}
