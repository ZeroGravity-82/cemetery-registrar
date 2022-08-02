<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * An error occurred in processing the application request, i.e. an exception was thrown.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/omniti-labs/jsend
 */
class ApplicationErrorResponse extends ApplicationResponse
{
    public string  $status = 'error';
    public string  $message;
    public ?string $code;
    public ?object $data;

    public function __construct(
        string  $message,
        ?string $code = null,
        ?object $data = null,
    ) {
        $this->message = $message;
        $this->code    = $code;
        $this->data    = $data;
    }
}
