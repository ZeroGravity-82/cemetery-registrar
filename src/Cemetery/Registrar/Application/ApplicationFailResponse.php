<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * There was a problem with the data submitted, or some pre-condition of the application service call wasn't satisfied.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/omniti-labs/jsend
 */
class ApplicationFailResponse extends AbstractApplicationResponse
{
    public const FAILURE_TYPE_VALIDATION_ERROR = 'VALIDATION_ERROR';
    public const FAILURE_TYPE_NOT_FOUND        = 'NOT_FOUND';
    public const FAILURE_TYPE_DOMAIN_EXCEPTION = 'DOMAIN_EXCEPTION';

    public string $status = 'fail';

    public function __construct(
        public object $data,
    ) {}
}
