<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathView;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowCauseOfDeathResponse extends ApplicationSuccessResponse
{
    public function __construct(
        CauseOfDeathView $view,
    ) {
        $this->data = (object) [
            'view' => $view,
        ];
    }
}
