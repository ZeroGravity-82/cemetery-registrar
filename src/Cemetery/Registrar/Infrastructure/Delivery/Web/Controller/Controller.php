<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
