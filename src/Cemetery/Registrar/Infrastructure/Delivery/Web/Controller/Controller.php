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
     * Retrieves the string value of the user input by name from the request. If it is an empty string or the input
     * key does not exist, null will be returned.
     *
     * @param Request $request
     * @param string  $key
     *
     * @return string|null
     */
    protected function getInputString(Request $request, string $key): ?string
    {
        $value = $request->request->get($key, null);

        return $value !== null && \trim($value) !== '' ? $value : null;
    }

    /**
     * Retrieves the integer value of the user input by name from the request. If it is an empty string or the input
     * key does not exist, null will be returned.
     *
     * @param Request $request
     * @param string  $key
     *
     * @return int|null
     */
    protected function getInputInt(Request $request, string $key): ?int
    {
        $value = $request->request->get($key, null);

        return $value !== null && \trim($value) !== '' ? (int) $value : null;
    }

    /**
     * Retrieves the boolean value of the user input by name from the request. If the input key does not exist
     * (including when the corresponding checkbox is not checked), null will be returned.
     *
     * @param Request $request
     * @param string  $key
     *
     * @return bool|null
     */
    protected function getInputBool(Request $request, string $key): ?bool
    {
        $value = $request->request->get($key, null);

        return $value !== null
            ? $request->request->filter($key, null, \FILTER_VALIDATE_BOOL)
            : null;
    }
}
