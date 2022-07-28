<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * An error occurred in processing the application request, i.e. an exception was thrown.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/omniti-labs/jsend
 */
class ApplicationResponseError extends ApplicationResponse
{
    public string $status = 'error';

    public function __construct(
        public string  $message,
        public ?string $code = null,
        public ?object $data = null,
    ) {}
}
