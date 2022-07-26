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
    public readonly string $status;

    public function __construct(
        public readonly string     $message, // A meaningful, end-user-readable (or at the least log-worthy) message,
                                             // explaining what went wrong.
        public readonly ?string    $code,    // A string code corresponding to the error, if applicable (optional
                                             // field).
        public readonly ?\stdClass $data,    // A generic container for any other information about the error, i.e.
                                             // the conditions that caused the error, stack traces, etc. (optional
                                             // field).
    ) {
        $this->status = 'error';
    }
}
