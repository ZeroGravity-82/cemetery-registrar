<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

use Cemetery\Registrar\Domain\Model\Exception as DomainException;
use Cemetery\Registrar\Domain\Model\NotFoundException;
use Cemetery\Registrar\Infrastructure\DependencyInjection\ApplicationServiceLocator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ApplicationRequestBus
{
    /**
     * @param ApplicationServiceLocator $appServiceLocator
     */
    public function __construct(
        private readonly ApplicationServiceLocator $appServiceLocator,
    ) {}

    /**
     * Delegates application request execution to the appropriate service. The request is validated first. If any
     * exceptions are thrown, they will be converted into an application response of the appropriate type.
     *
     * @param ApplicationRequest $request
     *
     * @return ApplicationResponse
     */
    public function execute(ApplicationRequest $request): ApplicationResponse
    {
        $requestClassName = \get_class($request);
        $appRequestName   = \substr(strrchr($requestClassName, '\\'), 1);
        $appServiceId     = 'app.service.'.\strtolower(\str_replace('Request', '', $appRequestName));

        // Request DTO validation
        // $note = ???->validate($request);
//         if ($note->hasErrors()) {
//             // TODO dispatch application event with validation failure details
//             return new ApplicationResponseFail(            // The request was rejected due to validation errors
//                 (object) [
//                     'type' => ApplicationResponseFail::FAILURE_TYPE_VALIDATION_ERROR,
//
//                 ]
//             );
//         }

        /** @var ApplicationService $appService */
        $appService = $this->appServiceLocator->get($appServiceId);
        $appService->assertSupportedRequestClass($request);



        try {
            $response = $appService->execute($request);

            // TODO dispatch application event with successful response details
        } catch (DomainException $e) {
            // The request was rejected
            $failureType = ApplicationResponseFail::FAILURE_TYPE_DOMAIN_ERROR;
            if ($e instanceof NotFoundException) {
                $failureType = ApplicationResponseFail::FAILURE_TYPE_NOT_FOUND;
            }
            $response = new ApplicationResponseFail(
                (object) [
                    'type'    => $failureType,
                    'message' => $e->getMessage(),
                ]
            );

            // TODO dispatch application event with failure details
        } catch (\Throwable $e) {
            // An error occurred while processing the request
            $response = new ApplicationResponseError('При обработке запроса произошла внутренняя ошибка сервера.');

            // TODO dispatch application event with error details
        }

        return $response;
    }
}
