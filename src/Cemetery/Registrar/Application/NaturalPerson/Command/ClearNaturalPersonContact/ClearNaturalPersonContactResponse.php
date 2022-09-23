<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClearNaturalPersonContact;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClearNaturalPersonContactResponse extends ApplicationSuccessResponse
{
    public function __construct(
        string $id,
    ) {
        $this->data = (object) [
            'id' => $id,
        ];
    }
}
