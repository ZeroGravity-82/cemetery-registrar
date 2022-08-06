<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\SoleProprietor\Command\CreateSoleProprietor;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateSoleProprietorResponse extends ApplicationSuccessResponse
{
    public function __construct(
        string $id,
    ) {
        $this->data = (object) [
            'id' => $id,
        ];
    }
}
