<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

use Cemetery\Registrar\Domain\Model\Exception as DomainException;
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
     * @param $request
     *
     * @return ApplicationResponse
     */
    public function execute($request): ApplicationResponse
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
        try {
            $response = $appService->execute($request);     // The request was accepted and successfully processed

            // TODO dispatch application event with successful response details
        } catch (DomainException $e) {

            $failureType = null;       // TODO from exception sub-type
            $response    = new ApplicationResponseFail(   // The request was rejected
                (object) [
                    'type'    => $failureType,
                    'message' => $e->getMessage(),
                ]
            );

            // TODO dispatch application event with failure details
        } catch (\Throwable $e) {                           // An error occurred while processing the request
            $response = new ApplicationResponseError('При обработке запроса произошла внутренняя ошибка сервера.');

            // TODO dispatch application event with exception details
        }

        return $response;
    }
}
