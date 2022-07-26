<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * There was a problem with the data submitted, or some pre-condition of the application service call wasn't satisfied.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/omniti-labs/jsend
 */
class ApplicationResponseFail extends ApplicationResponse
{
    public readonly string $status;

    public function __construct(
        public readonly ?\stdClass $data, // Provides the wrapper for the details of why the application request
                                          // failed. If the reasons for failure correspond to POST values, the response
                                          // object's keys SHOULD correspond to those POST values.
    ) {
        $this->status = 'fail';
    }
}
