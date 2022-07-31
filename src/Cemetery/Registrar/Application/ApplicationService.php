<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class ApplicationService
{
    /**
     * Validates the application request.
     *
     * @param ApplicationRequest $request
     *
     * @return Notification
     *
     * @throws \LogicException when the request is not an instance of the supported class
     */
    abstract public function validate(ApplicationRequest $request): Notification;

    /**
     * Executes the application request.
     *
     * @param ApplicationRequest $request
     *
     * @return ApplicationSuccessResponse
     *
     * @throws Exception  when there was any issue within the domain
     * @throws \Throwable when any error occurred while processing the request
     */
    abstract public function execute(ApplicationRequest $request): ApplicationSuccessResponse;
}
