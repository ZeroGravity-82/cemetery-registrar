<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller;

use Cemetery\Registrar\Application\ApplicationResponse;
use Cemetery\Registrar\Application\ApplicationErrorResponse;
use Cemetery\Registrar\Application\ApplicationFailResponse;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Psr\Container\ContainerExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse as HttpJsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Security\Csrf\CsrfToken;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class Controller extends AbstractController
{
    /**
     * Checks that the CSRF token is valid.
     *
     * @throws \RuntimeException when CSRF token is invalid
     * @throws \RuntimeException when JSON is invalid
     */
    protected function assertValidCsrfToken(Request $request, string $csrfTokenId): void
    {
        $data      = $this->decodeRequestData($request);
        $csrfToken = $data['csrfToken'] ?? null;
        if (!$this->isCsrfTokenValid($csrfTokenId, $csrfToken)) {
            throw new \RuntimeException(
                'Ошибка проверки CSRF-токена, возможно он устарел. Попробуйте перезагрузить страницу.'
            );
        }
    }

    /**
     * Creates application service request from the JSON request.
     *
     * @throws \RuntimeException when JSON is invalid
     */
    protected function handleJsonRequest(Request $request, string $appServiceRequestClassName): mixed
    {
        $constructorArgs = [];
        $data            = $this->decodeRequestData($request);
        foreach (\array_keys(\get_class_vars($appServiceRequestClassName)) as $propertyName) {
            $constructorArgs[] = $data[$propertyName] ?? $request->attributes->get($propertyName) ?? null;
        }

        return new $appServiceRequestClassName(...$constructorArgs);
    }

    /**
     * Creates JSON HTTP response from the application service response.
     *
     * @throws \InvalidArgumentException when the application response is of an unsupported type
     */
    protected function buildJsonResponse(
        ApplicationResponse $appResponse,
        int                 $httpResponseSuccessStatus,
        ?CsrfToken          $csrfToken = null,
    ): HttpJsonResponse {
        $httpResponseData = (object) [
            'status' => $appResponse->status,
        ];
        switch (true) {
            case $appResponse instanceof ApplicationSuccessResponse:
                $httpResponseData->data = $appResponse->data;
                if ($csrfToken !== null) {
                    $httpResponseData->data->csrfToken = $csrfToken->getValue();
                }
                $httpResponse = $this->json($httpResponseData, $httpResponseSuccessStatus);
                break;
            case $appResponse instanceof ApplicationFailResponse:
                $httpResponseData->data = $appResponse->data;
                $httpResponseStatus     = match ($appResponse->data->failType) {
                    ApplicationFailResponse::FAILURE_TYPE_VALIDATION_ERROR => HttpResponse::HTTP_UNPROCESSABLE_ENTITY,
                    ApplicationFailResponse::FAILURE_TYPE_NOT_FOUND        => HttpResponse::HTTP_NOT_FOUND,
                    ApplicationFailResponse::FAILURE_TYPE_DOMAIN_EXCEPTION => HttpResponse::HTTP_CONFLICT,
                    default                                                => HttpResponse::HTTP_BAD_REQUEST,
                };
                $httpResponse = $this->json($httpResponseData, $httpResponseStatus);
                break;
            case $appResponse instanceof ApplicationErrorResponse:
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
     * Returns a CSRF token for the given ID.
     *
     * @throws \LogicException             when CSRF protection is not enabled
     * @throws ContainerExceptionInterface when an error occurred while retrieving the token manager from the container
     */
    protected function getCsrfToken(string $csrfTokenId): CsrfToken
    {
        if (!$this->container->has('security.csrf.token_manager')) {
            throw new \LogicException('CSRF protection is not enabled in your application. Enable it with the "csrf_protection" key in "config/packages/framework.yaml".');
        }

        return $this->container->get('security.csrf.token_manager')->getToken($csrfTokenId);
    }

    /**
     * Decodes JSON data of the request body.
     *
     * @throws \RuntimeException when JSON is invalid
     */
    private function decodeRequestData(Request $request): mixed
    {
        $data = \json_decode($request->getContent(), true);
        if ($data === null && !$request->isMethod(Request::METHOD_GET)) {
            // TODO add testing
            throw new \RuntimeException(\sprintf('Неверный формат JSON: "%s".', $request->getContent()));
        }

        return $data;
    }
}
