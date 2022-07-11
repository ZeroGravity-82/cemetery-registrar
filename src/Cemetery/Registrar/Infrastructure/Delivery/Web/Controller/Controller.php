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
     * Creates service request DTO from the JSON request.
     *
     * @param Request $request
     * @param string  $serviceRequestClassName
     *
     * @return mixed
     */
    protected function handleJsonRequest(Request $request, string $serviceRequestClassName): mixed
    {
        $constructorArgs = [];
        $requestData     = \json_decode($request->getContent(), true);
        if ($requestData === null && !$request->isMethod(Request::METHOD_DELETE)) {
            // TODO add testing
            throw new \RuntimeException(\sprintf('Неверный формат JSON: "%s"', $request->getContent()));
        }
        foreach (\array_keys(\get_class_vars($serviceRequestClassName)) as $propertyName) {
            if ($propertyName === 'id') {
                $requestData[$propertyName] = $request->attributes->get('id');
            }
            $constructorArgs[] = $requestData[$propertyName] ?? null;
        }

        return new $serviceRequestClassName(...$constructorArgs);
    }
}
