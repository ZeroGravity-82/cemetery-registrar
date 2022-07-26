<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * All went well, and (usually) some data was returned.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/omniti-labs/jsend
 */
class ApplicationResponseSuccess extends ApplicationResponse
{
    public readonly string $status;

    public function __construct(
        public readonly ?\stdClass $data, // Acts as the wrapper for any data returned by the applications service
                                          // call. If the call returns no data, data should be set to null.
    ) {
        $this->status = 'error';
    }
}
