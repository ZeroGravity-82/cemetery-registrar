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

    protected function hasProperty(ApplicationRequest $request, string $name): bool
    {
        $hasProperty = false;
        foreach (\array_keys(\get_class_vars(\get_class($request))) as $propertyName) {
            if ($propertyName === $name) {
                $hasProperty = true;
                break;
            }
        }

        return $hasProperty;
    }
}
