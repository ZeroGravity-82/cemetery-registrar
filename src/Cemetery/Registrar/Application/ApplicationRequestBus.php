<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;
use Cemetery\Registrar\Infrastructure\DependencyInjection\ApplicationServiceLocator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ApplicationRequestBus
{
    public function __construct(
        private readonly ApplicationServiceLocator $appServiceLocator,
    ) {}

    /**
     * Delegates application request execution to the appropriate service. The request is validated first. If any
     * domain exceptions are thrown, they will be converted into application fail response. All other exceptions will
     * be converted into application error response.
     */
    public function execute(ApplicationRequest $request): ApplicationResponse
    {
        $requestClassName = \get_class($request);
        $appRequestName   = \substr(strrchr($requestClassName, '\\'), 1);
        $appServiceId     = 'app.service.'.\strtolower(\str_replace('Request', '', $appRequestName));

        try {
            /** @var ApplicationService $appService */
            $appService = $this->appServiceLocator->get($appServiceId);
            $note       = $appService->validate($request);
            if ($note->hasErrors()) {
                // The request was rejected due to validation errors

                // TODO dispatch application event with validation failure details
                return new ApplicationFailResponse(
                    [
                        'failType' => ApplicationFailResponse::FAILURE_TYPE_VALIDATION_ERROR,
                        ...$note->toArray(),
                    ]
                );
            }

            $response = $appService->execute($request);

            // TODO dispatch application event with successful response details
        } catch (Exception $e) {
            // The request was rejected due to domain exception
            $failureType = match (true) {
                $e instanceof NotFoundException => ApplicationFailResponse::FAILURE_TYPE_NOT_FOUND,
                default                         => ApplicationFailResponse::FAILURE_TYPE_DOMAIN_EXCEPTION,
            };
            $response = new ApplicationFailResponse(
                [
                    'failType' => $failureType,
                    'message'  => $e->getMessage(),
                ]
            );

            // TODO dispatch application event with failure details
        } catch (\Throwable $e) {
            // An error occurred while processing the request
            $response = new ApplicationErrorResponse('При обработке запроса произошла внутренняя ошибка сервера.');

            // TODO dispatch application event with error details
        }

        return $response;
    }
}
