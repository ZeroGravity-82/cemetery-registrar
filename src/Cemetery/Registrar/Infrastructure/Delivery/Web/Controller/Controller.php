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
     * Creates command or query service request from the JSON request.
     *
     * @param Request $request
     * @param string  $serviceRequestClassName
     *
     * @return mixed
     */
    protected function handleJsonRequest(Request $request, string $serviceRequestClassName): mixed
    {
        $constructorArgs       = [];
        $requestBody           = \json_decode($request->getContent(), true);
        $isRequestBodyExpected = !\in_array($request->getMethod(), [
            Request::METHOD_GET,
            Request::METHOD_DELETE,
            Request::METHOD_TRACE,
            Request::METHOD_OPTIONS,
            Request::METHOD_HEAD,
        ]);
        if ($requestBody === null && $isRequestBodyExpected) {
            // TODO add testing
            throw new \RuntimeException(\sprintf('Неверный формат JSON: "%s"', $request->getContent()));
        }
        foreach (\array_keys(\get_class_vars($serviceRequestClassName)) as $propertyName) {
            if ($propertyName === 'id') {
                $requestBody[$propertyName] = $request->attributes->get('id');
            }
            $constructorArgs[] = $requestBody[$propertyName] ?? null;
        }

        return new $serviceRequestClassName(...$constructorArgs);
    }
}
