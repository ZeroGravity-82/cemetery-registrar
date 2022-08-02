<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * There was a problem with the data submitted, or some pre-condition of the application service call wasn't satisfied.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/omniti-labs/jsend
 */
class ApplicationFailResponse extends ApplicationResponse
{
    public const FAILURE_TYPE_VALIDATION_ERROR = 'validation error';
    public const FAILURE_TYPE_NOT_FOUND        = 'not found';
    public const FAILURE_TYPE_DOMAIN_EXCEPTION = 'domain exception';

    public string $status = 'fail';
    public object $data;

    public function __construct(
        object $data,
    ) {
        $this->data = $data;
    }
}
