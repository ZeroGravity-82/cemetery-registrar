<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller;

use Cemetery\Registrar\Application\ApplicationResponse;
use Cemetery\Registrar\Application\ApplicationResponseError;
use Cemetery\Registrar\Application\ApplicationResponseFail;
use Cemetery\Registrar\Application\ApplicationResponseSuccess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse as HttpJsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class Controller extends AbstractController
{
    /**
     * Checks that the CSRF token is valid.
     *
     * @param Request $request
     * @param string  $tokenId
     *
     * @throws \RuntimeException when CSRF token is invalid.
     */
    protected function assertValidCsrfToken(Request $request, string $tokenId): void
    {
        $data  = $this->decodeRequestData($request);
        $token = $data['token'] ?? null;
        if (!$this->isCsrfTokenValid($tokenId, $token)) {
            throw new \RuntimeException('Ошибка проверки CSRF-токена, возможно он устарел. Попробуйте перезагрузить страницу.');
        }
    }

    /**
     * Creates command or query service request from the JSON request.
     *
     * @param Request $request
     * @param string  $serviceRequestClassName
     *
     * @return mixed
     */
    protected function handleJsonRequest(Request $request, string $serviceRequestClassName): mixed
    {
        $constructorArgs = [];
        $data            = $this->decodeRequestData($request);
        if ($data === null && !$request->isMethod(Request::METHOD_GET)) {
            // TODO add testing
            throw new \RuntimeException(\sprintf('Неверный формат JSON: "%s"', $request->getContent()));
        }
        foreach (\array_keys(\get_class_vars($serviceRequestClassName)) as $propertyName) {
            if ($propertyName === 'id') {
                $data[$propertyName] = $request->attributes->get('id');
            }
            $constructorArgs[] = $data[$propertyName] ?? null;
        }

        return new $serviceRequestClassName(...$constructorArgs);
    }

    /**
     * @param ApplicationResponse $appResponse
     * @param int                 $successStatus
     *
     * @return HttpJsonResponse
     */
    protected function buildJsonResponse(ApplicationResponse $appResponse, int $successStatus): HttpJsonResponse
    {
        $httpResponseData = (object) [
            'status' => $appResponse->status,
        ];
        switch (true) {
            case $appResponse instanceof ApplicationResponseSuccess:
                $httpResponseData->data = $appResponse->data;
                $httpResponse           = $this->json($httpResponseData, $successStatus);
                break;
            case $appResponse instanceof ApplicationResponseFail:
                $httpResponseData->data = $appResponse->data;
                $httpResponse           = $this->json($httpResponseData, HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
                break;
            case $appResponse instanceof ApplicationResponseError:
                $httpResponseData->message = $appResponse->message;
                if ($appResponse->code !== null) {
                    $httpResponseData->code = $appResponse->code;
                }
                if ($appResponse->data !== null) {
                    $httpResponseData->data = $appResponse->data;
                }
                $httpResponse = $this->json($httpResponseData, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
                break;
            default:
                throw new \InvalidArgumentException(\sprintf(
                    'Неподдерживаемый тип ответа прикладного сервиса: "%s".',
                    \get_class($appResponse),
                ));
        }

        return $httpResponse;
    }

    /**
     * Decodes JSON data of the request body.
     *
     * @param Request $request
     *
     * @return mixed
     */
    private function decodeRequestData(Request $request): mixed
    {
        return \json_decode($request->getContent(), true);
    }
}
