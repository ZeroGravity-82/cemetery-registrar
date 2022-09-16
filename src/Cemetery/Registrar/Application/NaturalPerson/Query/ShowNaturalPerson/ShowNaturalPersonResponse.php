<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\ShowNaturalPerson;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonView;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowNaturalPersonResponse extends ApplicationSuccessResponse
{
    public function __construct(
        NaturalPersonView $view,
    ) {
        $this->data = (object) [
            'view' => $view,
        ];
    }
}
