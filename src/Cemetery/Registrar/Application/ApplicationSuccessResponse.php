<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * All went well, and (usually) some data was returned.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/omniti-labs/jsend
 */
class ApplicationSuccessResponse extends ApplicationResponse
{
    public string  $status = 'success';
    public ?object $data   = null;
}
